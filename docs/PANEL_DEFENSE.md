# Panel Defense — Hospital Appointment Management System

> A reference of panel-ready answers for every architectural and implementation
> decision in this project. Each Q&A is short enough to deliver verbally without
> rambling. This document is **iterative**: it is updated at the end of each
> build phase as new decisions land.
>
> **How to use this in your defense:**
> Skim a section the night before. Don't memorise — internalise the *why*.
> When asked a question, answer in 1-3 sentences and stop; let the panel
> follow up if they want more depth.
>
> Last updated: end of Phase 9 (consultation notes).

---

## Table of contents

1. [Stack & technology choices](#1-stack--technology-choices)
2. [Database design](#2-database-design)
3. [Authentication & authorization](#3-authentication--authorization)
4. [Booking & conflict prevention](#4-booking--conflict-prevention)
5. [Appointment status state machine](#5-appointment-status-state-machine)
6. [Security](#6-security)
7. [Performance & scalability](#7-performance--scalability)
8. [UI / UX](#8-ui--ux)
9. [Build process & methodology](#9-build-process--methodology)
10. [Specific implementation decisions](#10-specific-implementation-decisions)
11. [Phase 9-14 anticipated questions (TBD)](#11-phase-9-14-anticipated-questions-tbd)

---

## 1. Stack & technology choices

**Q: Why PHP and Laravel instead of a more modern stack like Node/TypeScript or Go?**
A: Laravel is the industry standard for relational CRUD applications. It bundles authentication, ORM, migrations, validation, queues, and email into one framework, which is the right tradeoff for a single-developer 14-day project. Modern JS stacks would require gluing 5-6 separate libraries together for the same outcome. Go and Rust are optimised for high-throughput, low-latency systems — overkill for an outpatient hospital with maybe 50 requests per second peak load.

**Q: Why MySQL instead of PostgreSQL or Firebase?**
A: The hospital domain is inherently relational — patients, doctors, appointments, prescriptions all have foreign-key relationships. A document store like Firebase would force denormalisation, violating 3NF and creating data-integrity risk. PostgreSQL would also work, but MySQL is the dominant database in PHP/Laravel ecosystems and what panel members are most familiar with.

**Q: Why MySQL 9 when the spec said MySQL 8?**
A: MySQL 9 was the current Homebrew default at install time. The schema and queries used here are MySQL 8-compatible — there are no version-specific features in use. The decision is reversible by downgrading via Homebrew if needed.

**Q: Why Bootstrap 5 instead of Tailwind?**
A: Bootstrap is component-oriented and panel-friendly — every CSS class maps to a documented utility or component, making the code readable to examiners who have used Bootstrap before. Tailwind is utility-first which produces clean DOM but requires deeper familiarity to read. The locked stack picked Bootstrap 5 specifically for that defensibility.

**Q: Why server-rendered Blade templates instead of a React/Vue SPA?**
A: The interface is form-driven and CRUD-heavy — list doctors, fill a booking form, view appointments. Server-side rendering is the natural fit. Adding an SPA framework would have tripled the build complexity (separate frontend build, API design, state management) for no UX gain.

**Q: Why Alpine.js for reactivity?**
A: Alpine provides the small amount of client-side reactivity the booking form needs (showing the reason field after a slot is picked) in 14kb. A full SPA framework for the same outcome would cost 200kb+. Alpine integrates directly with Blade attributes (`x-data`, `x-show`) so there's no separate frontend codebase.

**Q: Why PHP 8.4 specifically?**
A: Laravel 11's officially tested PHP versions are 8.2-8.4. Homebrew installed PHP 8.5 by default, which deprecated `PDO::MYSQL_ATTR_SSL_CA` constants that Laravel 11 uses internally — producing visible deprecation warnings on every page render. Switching to 8.4 matches the framework's supported range and eliminates the warnings without code changes.

---

## 2. Database design

**Q: Why 10 tables in third normal form?**
A: 3NF eliminates redundancy and update anomalies — each fact is stored exactly once. The 10-table breakdown maps directly to the domain entities: users (auth), patients/doctors (role-specific data), departments, doctor_schedules (when doctors work), appointments (the join), and the consultation aftermath (medical_records, prescriptions, prescription_items, payments). Smaller schemas would force compromises like storing demographics on the users table.

**Q: Why is the role enum on `users` instead of a separate roles table with a many-to-many?**
A: Each user is exactly one role in this domain — there are no doctor-admins or patient-doctors. A many-to-many would over-engineer for a degree of flexibility we don't need, while making queries like "list all patients" require a join. The enum constraint at the DB level enforces the same integrity.

**Q: Why is patient and doctor data split into their own tables instead of being on `users`?**
A: Patients have demographic columns (date_of_birth, blood_group, address) that don't apply to doctors; doctors have professional columns (license_number, specialization, consultation_fee) that don't apply to patients. Storing both on `users` would mean roughly half the columns being NULL for any given row. Splitting follows the 3NF rule that columns should be a fact about the table's primary key.

**Q: Why ON DELETE CASCADE for some foreign keys and RESTRICT for others?**
A: CASCADE is used where the child record only exists *because of* the parent — a Doctor row only makes sense if the User exists, so deleting the user cascades to the doctor row. RESTRICT is used where the child preserves audit history that must outlive the parent — appointments must not silently disappear when a department is deleted, so departments can't be deleted while doctors reference them, and similarly doctors can't be deleted while appointments reference them.

**Q: Why no soft deletes?**
A: Soft deletes were not in the locked schema. Where audit history matters (appointments, medical records), the RESTRICT foreign keys prevent unintended deletion. Where a record genuinely needs to be removed (a department typo), hard delete is the correct semantic.

**Q: Why decimal(10,2) for money columns instead of float?**
A: Floats use binary representation that cannot exactly represent decimal currency values — 0.1 + 0.2 in floating point is not 0.3. decimal(10,2) stores up to 99,999,999.99 with exact decimal precision, which is the standard for currency in MySQL.

**Q: Why are `appointment_date` and `appointment_time` separate columns instead of one DATETIME?**
A: They model different concerns. The date is what's selected on the calendar; the time is what's selected on the slot picker. Querying for "all appointments today" or "all 10:00 AM slots" is cleaner with separate columns. The locked schema specified them this way and the controllers compose them with Carbon when needed.

**Q: Why `varchar(50)` for license number?**
A: Medical license numbers from various jurisdictions fit comfortably within 50 characters. The unique index enforces no two doctors can share one. A larger column wastes storage on the index pages.

**Q: Why is `slot_duration` an unsignedInteger (minutes) instead of a TIME?**
A: Slot durations are intervals, not times of day. An integer of minutes is the cleanest representation and works directly with Carbon's `addMinutes($schedule->slot_duration)`.

---

## 3. Authentication & authorization

**Q: Why use Laravel Breeze instead of building authentication from scratch?**
A: Breeze is Laravel's official authentication starter kit. It provides login, registration, password reset, and email verification with tested controllers, hashed passwords, CSRF protection, and session management — all features we'd otherwise have to build ourselves and risk introducing security bugs. Building auth from scratch is a known anti-pattern.

**Q: How does role-based access control work?**
A: The `users.role` enum (admin/doctor/patient) is read by a custom `EnsureUserHasRole` middleware. Routes are wrapped in `Route::middleware(['auth', 'role:admin'])` groups, so attempting to visit `/admin/dashboard` as a patient returns 403 Forbidden. The middleware is registered as the `role` alias in `bootstrap/app.php`.

**Q: How does role-based redirect work after login?**
A: Breeze redirects every successful login to the route named `dashboard`. We made `/dashboard` a small dispatcher route that reads `Auth::user()->role` and `redirect()->route("$role.dashboard")` — so an admin lands on `/admin/dashboard`, a doctor on `/doctor/dashboard`, a patient on `/patient/dashboard`. Each role-specific route is protected by the `role:` middleware.

**Q: Why is registration patient-only?**
A: In real hospitals, doctors and admins are credentialed and onboarded by HR/admin staff, not by walk-up self-registration. Allowing self-registration with role selection would be a security flaw — anyone could claim to be a doctor. The `RegisteredUserController` always sets `role = 'patient'` and creates a Patient row in the same transaction; admins create doctor accounts via the admin UI, which generates a temporary password.

**Q: How are passwords stored?**
A: Laravel's User model has `password => 'hashed'` cast, which uses bcrypt with a configurable cost factor. We never store plaintext passwords. The `Hash::make()` and `password_verify()` flow is handled by Breeze's controllers; we don't roll our own.

**Q: How is CSRF protection handled?**
A: All non-GET HTML forms include `@csrf` which inserts a hidden `_token` input. Laravel's middleware verifies the token matches the session's CSRF token; mismatched requests return 419. AJAX requests would use the meta tag `<meta name="csrf-token">` set in the layouts.

**Q: How do you prevent a logged-in patient from viewing another patient's appointments?**
A: Each protected controller method checks `abort_if($appointment->patient_id !== Auth::user()->patient->id, 403)`. This is per-record authorization on top of role middleware — the middleware confirms "is this user a patient" and the abort confirms "is this their record". Same pattern for doctors viewing appointments.

**Q: What happens if a doctor account is deleted while they have appointments?**
A: The `appointments.doctor_id` foreign key has `ON DELETE RESTRICT`, so the database refuses to delete the doctor row. The admin UI pre-checks for existing appointments and refuses with a friendly error before the SQL even runs, but the DB constraint is the bedrock that prevents the inconsistency even if the application check is bypassed.

---

## 4. Booking & conflict prevention

**Q: How do you prevent two patients from booking the same slot at the same time?**
A: Each booking runs inside a database transaction. Before inserting the appointment, the controller acquires a row-level write lock with `SELECT ... FOR UPDATE` matching `(doctor_id, date, time)` and excluding cancelled rows. If two patients try to book the same slot simultaneously, the second one's transaction blocks at the lock until the first commits, then re-runs the SELECT, sees the new row, and aborts with a friendly error. Cancellations don't conflict because the availability check excludes `status='cancelled'`.

**Q: Why use row locking instead of a database unique constraint on `(doctor_id, date, time)`?**
A: A unique constraint would conflict with cancelled appointments — once a patient cancels their 10:00 AM slot, no one else could book it because the cancelled row still occupies the unique index. Row locking lets us keep cancelled rows for audit history while still preventing live double-bookings. The cost is one `SELECT FOR UPDATE` query per booking, which is negligible.

**Q: How are time slots generated?**
A: Slots are derived on-demand by `App\Services\SlotService`. Given a doctor and a date, the service looks up the matching `doctor_schedule` row by day-of-week, then walks from `start_time` to `end_time` in `slot_duration`-minute steps, generating slot tuples. For each slot it checks `is_past` (against now) and `is_booked` (against active appointments). The result is a Collection.

**Q: Why aren't slots stored in the database?**
A: Pre-generating slots would create roughly 16 rows per doctor per workday — for 10 doctors over a year that's ~40,000 rows of redundant data, all derivable from `doctor_schedules` and `appointments`. On-demand generation is cheap (one query, in-memory loop) and means schedule changes take effect immediately without a backfill.

**Q: What happens to existing appointments if a doctor's schedule changes?**
A: Existing appointments are rows in the `appointments` table, not slot derivations, so they remain on the books at their booked time even if the doctor's schedule no longer covers that time. New bookings would only generate slots from the updated schedule. The admin would handle schedule conflicts manually — out of scope for the current build.

**Q: Can a patient book the same slot they previously cancelled?**
A: Yes. The cancellation sets `status='cancelled'` but keeps the row for audit. The availability check (`status != 'cancelled'`) treats that slot as free, and the new booking inserts a fresh appointment row. Two appointments for the same slot can coexist as long as only one is non-cancelled.

**Q: Why a 30-minute slot duration?**
A: It's stored per-schedule (`doctor_schedules.slot_duration`) so each doctor can have their own. The seeders default to 30 minutes because it's a realistic outpatient consultation length. The slot generator and booking flow both read this value from the schedule, so changing it for a specific doctor only requires editing their schedule row.

**Q: How do you handle bookings for past times?**
A: The slot generator marks slots in the past with `is_past = true`, which the form renders as crossed-out and disabled. The store action also validates `appointment_date >= today`, and re-runs the slot generator server-side to confirm the chosen slot is still available — so a stale form that POSTs a now-past time gets rejected.

**Q: How are dates restricted to weekdays only?**
A: The seeders only create schedules for Monday-Friday. The `bookableDatesFor()` method on `SlotService` walks the next 30 days and only includes dates whose day-of-week matches an existing schedule. So if a doctor never works Saturdays, Saturdays don't appear in the date picker.

---

## 5. Appointment status state machine

**Q: What status values can an appointment have?**
A: Per the locked schema, the `status` enum is one of: `pending`, `confirmed`, `completed`, `cancelled`, `no_show`. Default is `pending` when a patient books; the doctor moves it through subsequent states.

**Q: What status transitions are allowed?**
A: The `Doctor\AppointmentController` enforces a state machine: `pending → confirmed | cancelled` and `confirmed → completed | cancelled | no_show`. The terminal states (`completed`, `cancelled`, `no_show`) cannot transition further. Patients can only set `cancelled` from `pending` or `confirmed` via their own cancel action.

**Q: Why is `completed` terminal? Can a doctor un-complete by mistake?**
A: Once an appointment is marked completed, the consultation has happened and any associated records (medical_records, prescriptions in Phase 9+) are tied to that state. Allowing un-complete would risk medical-record integrity. The application enforces this; if a real mistake happens, the admin can correct it directly in the database.

**Q: What happens when a patient cancels a confirmed appointment?**
A: The patient's cancel action sets `status='cancelled'`. The appointment moves from "Upcoming" to "Past & Cancelled" in the patient view. The doctor sees the same status change in their list. The slot becomes available for new bookings because the slot generator excludes cancelled rows.

**Q: Can a doctor reject a booking?**
A: A doctor can transition `pending → cancelled`, which is effectively a rejection. There is no separate "rejected" status — the reason is communicated out of band, and the audit row preserves the timeline.

---

## 6. Security

**Q: How do you protect against SQL injection?**
A: All database queries use Eloquent or Laravel's query builder, which use parameterised PDO statements under the hood — user input is never concatenated into SQL strings. Validation rules also constrain types and shapes (e.g., `exists:departments,id`) so even if a parameter slipped through, it would still be a typed value.

**Q: How do you protect against XSS?**
A: Blade's `{{ $variable }}` syntax automatically escapes HTML entities, converting `<script>` to `&lt;script&gt;` before rendering. We never use `{!! $raw !!}` for user-supplied content. The Content-Security-Policy could be hardened in production, but the default escaping handles the common cases.

**Q: How do you handle authorization on individual records?**
A: Per-record checks use `abort_if(...)` at the start of show/destroy/update methods, comparing the record's owner FK to `Auth::user()`'s related ID. For example, `abort_if($appointment->patient_id !== Auth::user()->patient->id, 403)` ensures patients only see their own appointments. This is on top of role middleware that gates the route entirely.

**Q: Why use `$fillable` for mass assignment?**
A: Laravel models default to "guarded" — `Model::create($request->all())` is blocked unless `$fillable` whitelists each column. This prevents an attacker from POSTing extra fields like `role=admin` to escalate privileges. `$fillable` only includes columns the user is allowed to control.

**Q: How are sessions managed?**
A: Sessions use the `database` driver (configured in `.env`), so session data is stored in the `sessions` table. Session cookies are signed and HTTP-only by default, preventing JavaScript from reading them. Logout invalidates the session and regenerates the CSRF token.

**Q: How are passwords reset?**
A: Breeze provides a `/forgot-password` flow that emails a signed reset token (Phase 10 will turn on real SMTP; currently in `log` driver, the email lands in `storage/logs/laravel.log`). The token is single-use and time-bound. The reset form requires the token plus the user's email and new password.

**Q: How are temporary doctor passwords handled?**
A: When an admin creates a doctor, the controller generates a 12-character alphanumeric password via `Str::password(12, true, true, false)`, stores the hashed version, and surfaces the plaintext exactly once in the success flash message. The admin is expected to share it out-of-band (in person, secure messaging). The doctor can change it via `/profile` after first login.

---

## 7. Performance & scalability

**Q: How would this scale to 100,000 patients?**
A: The current schema and queries handle that comfortably. Foreign-key indexes are auto-created by Laravel migrations. The hot paths (booking flow, doctor's daily schedule, patient appointment list) all filter by indexed columns. The bottleneck would arrive at maybe 1M+ daily appointment events, where queue-based booking and read replicas would matter — out of scope for an outpatient hospital.

**Q: Are there any N+1 query risks?**
A: All list views use `with(['doctor.user', 'doctor.department'])` or similar eager-loading on the relationships rendered in the loop. The `index` methods in both Patient and Doctor `AppointmentController` eager-load the relevant nested relations. Without eager-loading, rendering 50 appointments could trigger 100+ queries; with it, it's 3.

**Q: What database indexes exist?**
A: Foreign-key columns get an index automatically from Laravel's `foreignId()->constrained()`. Unique columns (`users.email`, `departments.name`, `doctors.license_number`) get unique indexes. The `appointments` table is queried by `(doctor_id, appointment_date, appointment_time)` for slot lookup; the FK index on `doctor_id` plus the small per-doctor row count makes this fast in practice. A composite index could be added if profiling showed a need.

**Q: Why didn't you cache the slot generation?**
A: At our scale, generating 16 slots per request is microseconds — caching would add complexity (cache invalidation on booking) for negligible benefit. If profiling showed it was a hot path, a per-doctor-per-date cache key could be added.

---

## 8. UI / UX

**Q: How are validation errors shown to the user?**
A: Forms apply Bootstrap's `is-invalid` class via `@error('field') is-invalid @enderror` on the input, and the message is rendered in an `.invalid-feedback` div. Old input is preserved with `old('field')` so users don't lose their work on a failed submission.

**Q: How are flash messages handled?**
A: Successful actions redirect with `->with('success', '...')` (or 'error' for failures). The shared layout reads `session('success')` and `session('error')` and renders Bootstrap alerts at the top of the page; alerts are dismissible.

**Q: Why are some views structured with `<x-slot name="header">`?**
A: That's Breeze's anonymous component pattern. The shared `layouts/app.blade.php` defines a header slot that consuming pages fill with their own h2. It keeps the navbar and chrome consistent across pages without each view duplicating layout markup.

**Q: How does the booking form know when to show the reason field?**
A: Alpine.js — `<form x-data="{ time: '' }">` initialises a reactive variable, slot buttons use `@click="time = '<slotvalue>'"` to set it, and the reason field uses `x-show="time"` to appear only after a slot is picked. This is the small amount of client-side reactivity we need; everything else is server-rendered.

**Q: Why does the doctor's appointment status update use a confirm() dialog instead of a Bootstrap modal?**
A: `confirm()` is two lines of inline HTML, works with no JS infrastructure, and is sufficient for a destructive action that doesn't need a pretty UI. A modal would be appropriate later if we needed to capture additional input on the confirmation (e.g., a no-show reason).

---

## 9. Build process & methodology

**Q: Why was this project built in 14 sequential phases?**
A: Each phase delivers a specific, testable layer. Phase 3 (migrations) must work before Phase 4 (models) can map to tables. Phase 4 must work before Phase 5 (seeders) can create test data. Building in order reduces compounding bugs — if Phase 8 booking fails, the bug is in Phase 8, not in three earlier layers I forgot to verify.

**Q: Why did you commit at the end of every phase?**
A: Each commit is a stable, working snapshot. If a later phase breaks something, `git revert` or `git reset` can move back to a known-good point. The commit messages also document the reasoning for each chunk of work, which is useful for both panel review and future maintenance.

**Q: How was AI used in this project?**
A: I worked alongside an AI coding assistant (Claude Code) for code generation and architectural advice, while making the design and verification decisions myself. Each commit includes a `Co-Authored-By: Claude` trailer to be transparent about that collaboration. AI accelerated the build but did not replace the judgment of which approach to take, when to push back on suggestions, or when to verify the result.

**Q: Why are there no automated tests?**
A: Laravel's test suite is set up (Breeze ships with auth feature tests). Phase 14 of the plan covers polish and testing — that's where I'd add test coverage for the booking conflict logic, status transitions, and authorization rules. For the build phase, manual testing and tinker-based smoke tests gave faster feedback.

**Q: What is your git workflow?**
A: Single `main` branch, one commit per phase. The commit message includes a phase summary, a bullet list of changes, the verification done, and the AI co-authoring trailer. No remote — local-only for the academic build; a GitHub remote can be added at any point.

---

## 10. Specific implementation decisions

**Q: Why is the test admin email `admin@hospital.test`?**
A: `.test` is an IANA-reserved TLD that will never resolve to a real domain — safe for test data. Using `admin@hospital.test` keeps the seeded test users isolated from any real email infrastructure that might exist in dev or prod environments.

**Q: Why is the seeded data mixed between hand-coded and Faker-generated?**
A: Hand-coded for stable entities (the 7 departments, the predictable login emails) so you always know the structure. Faker-generated for variable demographics (names, addresses, fees) so each seed produces a different-looking but equivalent hospital. Both seed runs leave a deterministic *count* of records.

**Q: Why are departments named generically (Cardiology, Pediatrics) instead of mimicking a real hospital?**
A: They are panel-recognisable specialties that map cleanly to the `specialization` field on doctors. A real hospital's department list would be more idiosyncratic and add noise to the demo without adding educational value.

**Q: How does the navbar know which links to show for each role?**
A: `Auth::user()->isAdmin()`, `isDoctor()`, `isPatient()` helper methods on the User model return booleans the navbar uses with `@if` blocks. The methods compare `$this->role` to a constant string. They centralise the role check so a future enum class refactor only changes one place.

**Q: Why does the admin onboard-doctor success message contain the temp password in plaintext?**
A: It's the simplest way to surface a one-time secret without building an in-app secrets vault. The message is rendered once in the redirect-back-flash; it's not logged, not stored, not emailed in Phase 8 (Phase 10 will replace this with an emailed credential). The admin is expected to share it via secure channels.

**Q: Why does `routes/web.php` import controllers with aliases like `AdminDashboardController as ...`?**
A: Multiple controllers across different namespaces have the same short name (`DashboardController` exists in `Admin\`, `Doctor\`, and `Patient\`). Importing with aliases avoids namespace ambiguity in `Route::get(...)` calls and keeps the routes file readable.

**Q: Why is the `welcome` page's footer hard-coded with "AUST Abuja final-year project"?**
A: It's the academic context for this particular build. In a real production deployment, that footer would be replaced with the hospital's branding. The string lives only in `resources/views/welcome.blade.php` and `resources/views/layouts/app.blade.php` — easy to find and replace.

---

## 11. Phase 9-14 anticipated questions (TBD)

These will be filled in as each phase ships. Topics covered:

### Phase 9 — Doctor consultation notes (medical records)

**Q: Why are medical records tied to appointments rather than to patients directly?**
A: Each consultation produces one record, and the appointment is the natural anchor — it knows the date, the doctor, and the reason for visit. Tying the record to the appointment also encodes "this diagnosis came from a real visit on this date" without duplicating that metadata. Patients access their records by joining through their appointments via `whereHas('appointment', ...)` in the controller.

**Q: Why is there one medical record per appointment instead of many?**
A: A single visit produces a single consultation record. If a follow-up is needed, that's a new appointment with its own record. The schema enforces one-to-one via `appointments.hasOne(MedicalRecord)` and the foreign key on `medical_records.appointment_id`.

**Q: How are diagnosis and notes validated?**
A: `diagnosis` is required, max 5000 characters. `notes` is optional, max 10000 characters. Both are stored as TEXT columns. Limits prevent abuse and keep the table performant; 5000/10000 is generous for real-world consultation content.

**Q: When can a doctor write or edit notes?**
A: Only when the appointment status is `confirmed` or `completed`. The controller enforces this with `abort_if(! in_array($appointment->status, ['confirmed','completed']), 422)`. Pending status means the patient hasn't been seen yet; cancelled or no_show means no consultation took place.

**Q: Can a doctor edit notes after marking the appointment completed?**
A: Yes. Doctors sometimes realise errors or need to add follow-up details after the visit. Editing remains allowed as long as the appointment is `confirmed` or `completed`. The `updated_at` timestamp records when the change was made, providing an audit trail.

**Q: How do you prevent a doctor from writing notes on another doctor's appointment?**
A: `Doctor\MedicalRecordController` calls `abort_if($appointment->doctor_id !== Auth::user()->doctor->id, 403)` on every store/update. The route is also gated by `role:doctor` middleware. Two layers of authorisation: middleware confirms the user is a doctor; the abort confirms it's *their* appointment.

**Q: How do you prevent a patient from viewing another patient's medical record?**
A: `Patient\MedicalRecordController::show` calls `abort_if($record->appointment->patient_id !== Auth::user()->patient->id, 403)`. The `index` action filters by joining through appointment with `whereHas('appointment', fn($q) => $q->where('patient_id', $patientId))` — it's impossible to list records belonging to another patient.

**Q: Why is the doctor's notes form embedded on the appointment show page instead of a dedicated URL?**
A: Notes are inherently bound to one appointment — a separate URL would require additional navigation back-and-forth between "view appointment" and "write notes". Embedding keeps the doctor in the context of the patient's visit. The form POSTs to a separate endpoint (`/doctor/appointments/{id}/notes`) so the controller architecture stays clean.

**Q: Why can the patient see medical records on a separate page (`/patient/records`) AND inline on the appointment detail?**
A: Two access patterns. `/patient/records` is for "show me my history" — appropriate when a patient wants to look up a past diagnosis. The inline view on the appointment detail is for "what came of this specific visit" — natural when reviewing one appointment. Both query the same data, just framed differently.

**Q: Why does Phase 9 not include prescriptions?**
A: The locked phase plan separates them: Phase 9 is "consultation notes" (medical_records); Phase 11 is "Prescriptions + PDF generation" with DomPDF. Splitting keeps each phase scoped — Phase 11 is mostly about DomPDF rendering, which is its own domain of complexity.

### Phase 10 — Email notifications (SMTP via SendGrid)
- Why SendGrid specifically?
- How are queued emails handled?
- What email events fire (booking confirmed, cancelled, etc.)?
- TBD

### Phase 11 — Prescriptions + DomPDF
- Why generate PDFs server-side instead of using browser print?
- How is the prescription form structured (one row per medication)?
- How are prescription PDFs served to the patient?
- TBD

### Phase 12 — Reports dashboard with Chart.js
- Why Chart.js over server-rendered charts?
- What metrics are reported and why?
- How is appointment data aggregated for the charts?
- TBD

### Phase 13 — Stripe test-mode payments
- Why Stripe test mode for an academic project?
- How is the payment flow integrated with the booking lifecycle?
- What happens if the patient closes the browser mid-payment?
- TBD

### Phase 14 — Polish, testing, deployment
- What automated tests cover the booking engine?
- How would this be deployed to production (server, env, secrets)?
- What's the rollback plan if a deployment breaks?
- TBD

---

## How this document is maintained

- I update this file at the **end of every phase** alongside the phase commit.
- New questions you encounter while preparing for the panel can be added under the relevant section — open an entry and we'll fill in a defensible answer.
- If a previous answer is no longer accurate (e.g., we change an FK rule), update it here in the same change as the code update.
