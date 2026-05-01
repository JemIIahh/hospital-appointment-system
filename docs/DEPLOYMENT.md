# Deployment Notes

What a real production deployment of this app would need. Out of scope for the academic build — documented here so the next person picking it up has a checklist.

---

## 1. Server requirements

| Component | Minimum |
|---|---|
| OS | Ubuntu 22.04+ or macOS Server |
| PHP | **8.4** with extensions: `mbstring`, `pdo_mysql`, `xml`, `tokenizer`, `bcmath`, `curl`, `gd` (DomPDF), `dom`, `fileinfo`, `openssl` |
| Web server | nginx 1.20+ (preferred) or Apache 2.4 |
| Database | MySQL 8 or 9 |
| Node.js | 20+ (for the build step only — not needed at runtime) |
| Process manager | `supervisord` or systemd (for queue worker) |
| TLS | Let's Encrypt via certbot |

RAM: 1 GB minimum, 2 GB comfortable.
Disk: ~500 MB for the app + Composer vendor + Node modules.

---

## 2. Environment variables to change

`.env` differences between local dev and production:

```ini
APP_ENV=production
APP_DEBUG=false              # critical — never true in prod
APP_URL=https://hospital.example.org

# Strong, unique key — generate with: php artisan key:generate
APP_KEY=base64:...

# Real database credentials, not root with no password
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hospital_prod
DB_USERNAME=hospital_app
DB_PASSWORD=<generated>

# Real SendGrid credentials (replace the dev log driver)
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=<sendgrid-api-key>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@hospital.example.org   # verified SendGrid sender
MAIL_FROM_NAME="Hospital Appointment System"

# Use the queue for emails so SMTP latency doesn't block requests
QUEUE_CONNECTION=database

# Sessions and cache stay on database (already configured)
SESSION_DRIVER=database
CACHE_STORE=database
```

---

## 3. Deployment steps (first time)

```bash
# Clone and install
git clone <repo-url> /var/www/hospital
cd /var/www/hospital
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Configure .env (see section 2)
cp .env.example .env
php artisan key:generate
nano .env

# Create the database, then migrate
mysql -u root -p -e "CREATE DATABASE hospital_prod;"
mysql -u root -p -e "CREATE USER 'hospital_app'@'localhost' IDENTIFIED BY 'STRONG-PASSWORD'; GRANT ALL ON hospital_prod.* TO 'hospital_app'@'localhost';"
php artisan migrate --force        # --force is required outside of dev

# Seed only departments + admin user, NOT the demo appointments
# (skip the full seed in prod; create production data manually)
php artisan db:seed --class=DepartmentSeeder --force

# Create the symlink for storage
php artisan storage:link

# Cache configuration, routes, views for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions (Laravel needs to write to these)
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 4. nginx config

`/etc/nginx/sites-available/hospital`:

```nginx
server {
    listen 80;
    server_name hospital.example.org;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name hospital.example.org;
    root /var/www/hospital/public;
    index index.php;

    ssl_certificate     /etc/letsencrypt/live/hospital.example.org/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/hospital.example.org/privkey.pem;

    # Standard security headers
    add_header X-Content-Type-Options "nosniff";
    add_header X-Frame-Options "DENY";
    add_header Referrer-Policy "strict-origin-when-cross-origin";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Built assets caching
    location ~* \.(css|js|woff2?|svg|png|jpg)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # Block access to sensitive paths
    location ~ /\.(env|git|htaccess) { deny all; }
}
```

Then:
```bash
sudo ln -s /etc/nginx/sites-available/hospital /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

---

## 5. HTTPS with Let's Encrypt

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d hospital.example.org
sudo systemctl enable certbot.timer    # auto-renew
```

---

## 6. Queue worker (for emails)

In production, set `QUEUE_CONNECTION=database` and run a worker so email sends don't block requests:

`/etc/supervisor/conf.d/hospital-worker.conf`:

```ini
[program:hospital-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/hospital/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/hospital-worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start hospital-worker:*
```

You'll also need to flip `Mailable` classes to `implements ShouldQueue` (one line per class) so they actually use the queue. Currently they send synchronously.

---

## 7. Cron (for any scheduled jobs)

If/when scheduled jobs are added (e.g., appointment reminder emails 24 hours before the visit), add a single cron line:

```cron
* * * * * cd /var/www/hospital && php artisan schedule:run >> /dev/null 2>&1
```

Currently no scheduled jobs are defined. The hook is wired (`routes/console.php`) but empty.

---

## 8. Backup strategy

Three things need backup:
1. **Database** — `mysqldump hospital_prod | gzip > /backups/db-$(date +%F).sql.gz` daily, retained for 30 days
2. **`.env`** — store securely (1Password, Vault, etc.); never commit
3. **`storage/app/`** — any uploaded files; not currently used by the app but reserved

`storage/logs/` does not need backup — it's regenerable, and rotating it weekly is sufficient.

---

## 9. Deployment updates

For subsequent deploys after the first:

```bash
cd /var/www/hospital
php artisan down                   # maintenance mode
git pull
composer install --no-dev --optimize-autoloader
npm install --production
npm run build

php artisan migrate --force        # if there are new migrations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reload PHP-FPM so it picks up new opcode cache
sudo systemctl reload php8.4-fpm

# Restart the queue worker so it picks up new code
sudo supervisorctl restart hospital-worker:*

php artisan up                     # exit maintenance
```

---

## 10. Monitoring (recommended, not required)

For a real hospital deployment:
- **Uptime monitoring** — UptimeRobot or BetterUptime hits `/up` (Laravel's built-in health endpoint, already configured)
- **Error tracking** — Sentry or Bugsnag (Laravel adapter is one config file)
- **Log aggregation** — ship `storage/logs/laravel.log` to Papertrail or Datadog
- **DB monitoring** — Percona Monitoring or RDS Performance Insights

None of these are wired in the current build. A real deployment would add them.

---

## What's NOT in this deployment doc

- Docker / Kubernetes — the app deploys fine to a single VM; container orchestration is overkill for a single hospital.
- Multi-region / read replicas — would matter at hospital-network scale (50+ hospitals); a single hospital doesn't need them.
- Stripe webhooks — payment integration was scoped out of this academic build (see `docs/PANEL_DEFENSE.md` §11 Phase 13).
