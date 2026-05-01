# Hospital Appointment Management System

A web-based hospital appointment system with three user roles (admin, doctor, patient), live booking with double-booking prevention, consultation notes, prescription PDFs, email notifications, and an admin reports dashboard.

Final-year project, **African University of Science and Technology (AUST), Abuja**.

---

## Tech stack

| Layer | Choice |
|---|---|
| Backend | **PHP 8.4**, **Laravel 11** |
| Database | **MySQL 9** (8-compatible) |
| Frontend | **Blade** templates, **Bootstrap 5.3**, **Alpine.js** |
| Charts | **Chart.js 4.4** (CDN) |
| PDFs | **DomPDF** (`barryvdh/laravel-dompdf`) |
| Email | Laravel Mailables, **SendGrid** SMTP (currently `log` driver) |
| Auth | **Laravel Breeze** (Blade flavor) + custom role middleware |
| Testing | **PHPUnit** with in-memory **SQLite** |

---

## Prerequisites

- PHP **8.4** (Laravel 11 doesn't support 8.5 yet — see `docs/PANEL_DEFENSE.md` §1)
- Composer 2.x
- MySQL 8 or 9
- Node.js 20+ and npm

On macOS:
```bash
brew install php@8.4 composer mysql node
brew link --overwrite --force php@8.4
brew services start mysql
```

---

## Local setup

```bash
# 1. Install backend dependencies
composer install

# 2. Install frontend dependencies and build assets
npm install
npm run build

# 3. Copy and configure environment
cp .env.example .env
php artisan key:generate

# Edit .env if your MySQL setup differs from defaults:
#   DB_DATABASE=hospital_appointment_system
#   DB_USERNAME=root
#   DB_PASSWORD=

# 4. Create the database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS hospital_appointment_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 5. Run migrations and seed sample data
php artisan migrate:fresh --seed

# 6. Start the dev server
php artisan serve
```

Open **http://127.0.0.1:8000**.

---

## Test credentials

After running `migrate:fresh --seed`, use any of:

| Role | Email | Password |
|---|---|---|
| Admin | `admin@hospital.test` | `password` |
| Doctor (1-10) | `doctor1@hospital.test` … `doctor10@hospital.test` | `password` |
| Patient (1-12) | `patient1@hospital.test` … `patient12@hospital.test` | `password` |

The seed data includes 7 departments, 10 doctors with weekday schedules, 12 patients, and ~70 appointments distributed across statuses (pending, confirmed, completed, cancelled, no-show) — most completed appointments have medical records, and ~70% of those have prescriptions.

---

## Running tests

```bash
php artisan test
```

49 feature tests covering:
- Booking conflict prevention (same-slot double booking → blocked)
- Role-based authorization (admin / doctor / patient route gating)
- Per-record authorization (patient A can't see patient B's data)
- Full appointment lifecycle (pending → confirmed → completed)
- Consultation-notes status gating (writable only on confirmed/completed)
- Prescription PDF download with authorization

Tests run on an in-memory SQLite database — no test data leaks into your dev MySQL.

---

## Folder structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/         (DashboardController, DepartmentController,
│   │   │                   DoctorController, ReportController)
│   │   ├── Auth/          (Breeze auth controllers)
│   │   ├── Doctor/        (DashboardController, AppointmentController,
│   │   │                   MedicalRecordController, PrescriptionController)
│   │   └── Patient/       (DashboardController, DoctorController,
│   │                       AppointmentController, MedicalRecordController,
│   │                       PrescriptionController)
│   └── Middleware/
│       └── EnsureUserHasRole.php   (role:admin|doctor|patient)
├── Mail/                  (5 transactional Mailables)
├── Models/                (10 Eloquent models matching the schema)
└── Services/
    └── SlotService.php    (slot generation + bookable-dates logic)

database/
├── factories/             (User, Department, Doctor, Patient, DoctorSchedule)
├── migrations/            (12 migrations: 3 Laravel core + 9 domain tables)
└── seeders/               (Department, User, Doctor, Patient, DoctorSchedule,
                            Appointment + auto-cascading medical records and
                            prescriptions for completed appointments)

resources/views/
├── admin/                 (dashboard, departments/*, doctors/*, reports/*)
├── doctor/                (dashboard, appointments/*)
├── patient/               (dashboard, doctors/*, appointments/*, records/*,
                            prescriptions/*)
├── auth/                  (Breeze auth views — Bootstrap-restyled)
├── emails/                (Markdown mailables)
├── layouts/               (app, guest)
├── pdfs/                  (prescription)
├── profile/               (Breeze profile pages)
└── welcome.blade.php

docs/
├── PANEL_DEFENSE.md       (panel-ready Q&A for every architectural decision)
└── DEPLOYMENT.md          (production deployment notes)
```

---

## Documentation

- **`docs/PANEL_DEFENSE.md`** — answers to every "why did you do X?" question a project panel might ask, organised by topic. Skim before defending.
- **`docs/DEPLOYMENT.md`** — what a real production deployment would need (server requirements, env settings, nginx config, queue workers, HTTPS).

---

## Useful commands

```bash
# Wipe DB and re-seed sample data
php artisan migrate:fresh --seed

# Re-seed just appointments (faster than full re-seed)
php artisan db:seed --class=AppointmentSeeder

# Drop all appointments without affecting users/departments
php artisan tinker --execute='App\Models\Appointment::query()->delete()'

# Check email rendering (mail driver=log writes to storage/logs/laravel.log)
tail -f storage/logs/laravel.log | grep -i "subject:"

# Run tests
php artisan test
php artisan test --filter=BookingConflictTest

# Show all routes
php artisan route:list
```

---

## Author

Built by **Jemiiah** (`seunjeremiah2003@gmail.com`) — final-year computer science, AUST Abuja, 2026.

This project was developed with assistance from Anthropic's Claude (commit history transparently records the collaboration via `Co-Authored-By` trailers). Architectural decisions, design choices, and final implementation judgments were the author's.
