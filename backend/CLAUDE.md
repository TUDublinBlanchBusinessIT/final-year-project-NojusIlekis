# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A childcare management web application built with Laravel 12, Tailwind CSS v3, Alpine.js, and Vite. It supports three user roles: **parent**, **carer**, and **manager**, each with a separate dashboard and feature set.

## Commands

All commands run from the `backend/` directory.

```bash
# Full dev environment (Laravel server + Vite + queue + log tail)
composer run dev

# First-time setup (install deps, generate key, migrate, build assets)
composer run setup

# Run all tests
composer test

# Run a specific test file
php artisan test tests/Feature/RoleAccessTest.php

# Run tests matching a name pattern
php artisan test --filter "blocks parent"

# Database migrations
php artisan migrate

# Seed the database (creates test users + sample data)
php artisan db:seed

# Format code (Laravel Pint)
./vendor/bin/pint

# Build frontend assets
npm run build
```

## Architecture

### Role-Based Access

The application uses a custom `RoleMiddleware` (`app/Http/Middleware/RoleMiddleware.php`) registered as `role:X` in routes. Routes are grouped by role prefix:

- `/parent/*` — parent role only
- `/carer/*` — carer role only
- `/manager/*` — manager role only

`User::dashboardRouteName()` returns the correct dashboard route for a given user's role. The `/dashboard` route redirects to the appropriate role dashboard using a `match` expression.

An `IdleTimeout` middleware enforces session expiry based on `session.lifetime` config.

### Data Model

```
User (role: parent|carer|manager)
  ↕ (pivot: room_user — room_id, user_id, start_date, end_date, is_primary)
Room (name, age_band)
  ↑ Child (room_id nullable, dob, allergies, medical_notes)
      ↕ (pivot: child_parent — child_id, parent_id, relationship_type, legal_guardian)
      User (role: parent)
      ↑ Attendance (child_id, date, status: present|absent, room_id, recorded_by)
      ↑ DailyReport (child_id, carer_id, date, daily_report text)
      |     ↑ MediaUpdate (daily_report_id)
      ↑ DailyUpdate (child_id, date, meals, sleep, notes, created_by)
      ↑ MedicationLog (child_id, carer_id, medication_name, dosage, date, time_given, notes)
      ↑ Invoice (child_id, parent_id, period_start, period_end, due_date, total, discount, status: draft|sent|paid)
            ↑ InvoiceItem (invoice_id, description, qty, unit_price, total)
      ↑ IncidentReport (child_id, carer_id, room_id, incident_date, incident_time, title, description, action_taken, severity: low|medium|high, parent_contact_required, status: open|reviewed|closed)

Acknowledgement (record_type, record_id, parent_id, status: pending|acknowledged, signed_at, signature_name)

Message (sender_id → users, receiver_id → users, child_id nullable → children, body text, read_at nullable timestamp)
  - Indexes: (sender_id, receiver_id) for conversation lookups; (receiver_id, read_at) for unread queries
  - User::sentMessages() / User::receivedMessages() — hasMany via sender_id / receiver_id
  - read_at = null means unread; filled when receiver opens the message
```

- Children no longer have a `parent_user_id` column — parent links use the `child_parent` pivot (supports multiple parents per child).
- `room_id` on `children` is nullable (child can be unassigned from a room).
- `room_user` unique constraint is on `(room_id, user_id, start_date)` — allows tracking carer room history. Use `User::activeRooms()` / `Room::activeCarers()` (where `end_date IS NULL`) to get current assignments.
- Attendance records store `room_id` directly (denormalised) to support efficient room-level filtering.
- Two data migration files use raw SQL — these must stay SQLite-compatible (subquery style, not MySQL `UPDATE…JOIN` or `DELETE alias FROM`).

### Controller Structure

```
app/Http/Controllers/
  Carer/
    AttendanceController    — mark children present/absent by room and date
    DailyReportController   — write/view written reports + media uploads per child
    DailyUpdateController   — structured updates (meals, sleep, notes) per child
    MedicationController    — log medication administered to children (scoped to carer's rooms)
    IncidentReportController — create/view incident reports; scoped to carer's rooms; validates child belongs to selected room
  Manager/
    DashboardController     — attendance KPI dashboard with date/room filters and trend chart
    ReportsController       — attendance and task reports
    DailyReportsController  — view carer-written reports; request parent acknowledgement
    MedicationLogsController — view all medication logs filtered by date/room
    InvoiceController        — create invoices for a child/parent, add line items, apply discount, update status, print view
    IncidentReportsController — view all incident reports filtered by date; update status (open/reviewed/closed)
    ChildController          — full CRUD for children (index with search/room filter, create, show profile, edit, delete); also handles parent linking (linkParentForm, linkParent, unlinkParent — max 2 parents per child, composite PK enforced) and inline room assignment (assignRoom via PATCH)
    ParentController         — full CRUD for parents (Users with role='parent'); uses $parentUser variable (not $parent — PHP reserved word); destroy() guards against deleting parents with linked children
    CarerController          — full CRUD for carers (Users with role='carer'); index supports search + active room filter; store/update handle room_user pivot assignment (attach with start_date, close old assignment by setting end_date); show displays current room, room history, and activity counts
  Parent/
    AcknowledgementController — list pending acknowledgements; sign with name + checkbox
    InvoiceController         — view own invoices (index + show); `abort_unless` guards invoice ownership
  ProfileController         — shared profile edit/delete
```

### Frontend

Blade templates in `resources/views/`, organised by role (`carer/`, `manager/`, `dashboards/`). Layouts are in `resources/views/layouts/` (`app.blade.php` for authenticated, `guest.blade.php` for auth screens). Alpine.js handles interactive UI behaviour inline. Charts on the manager dashboard use Chart.js data injected from Blade.

Manager views are in `resources/views/manager/` with subdirectories: `children/`, `parents/`, `carers/` (each with index, create, edit, show). All CRUD views share a consistent Tailwind styling pattern: `rounded-2xl` cards with `border-slate-200` borders, blue gradient buttons (`from-blue-600 via-blue-700 to-indigo-700`), amber badge for "Unassigned", red badge styling for destructive actions.

Manager navigation (in `navigation.blade.php`) shows role-specific links: **Dashboard → Children → Parents → Carers** for managers. Each link uses `request()->routeIs('manager.X.*')` for active state.

### Testing

Tests use Pest PHP with an in-memory SQLite database (configured in `phpunit.xml`). The `tests/Feature/` directory contains role-access tests, auth tests, and feature tests. Use `User::factory()->create(['role' => 'carer'])` pattern to create role-specific users in tests. `ChildFactory` and `RoomFactory` are now available for use in tests.

## Seed Test Accounts

All seed users have password `Password123!`:

| Email | Role |
|---|---|
| parent@test.com | parent |
| carer@test.com | carer |
| manager@test.com | manager |
| parent2@test.com | parent |

Seed rooms: **Bumblebees** and **Ladybirds**. The carer is assigned to both rooms.

## API (Sanctum Token Auth)

The app exposes a token-based JSON API at `/api/*` for mobile clients using Laravel Sanctum v4.

### Auth endpoints (public)

| Method | URL | Description |
|---|---|---|
| POST | `/api/register` | Register new user, returns token |
| POST | `/api/login` | Login with email + password, returns token |

### Authenticated endpoints (Bearer token required)

| Method | URL | Description |
|---|---|---|
| GET | `/api/user` | Returns authenticated user info |
| POST | `/api/logout` | Revokes current token |

### Role-scoped endpoints

**Parent** (`/api/parent/*` — requires `role=parent`):

| Method | URL | Description |
|---|---|---|
| GET | `/api/parent/children` | List parent's linked children with room |
| GET | `/api/parent/children/{child}` | Single child + recent attendances, daily reports, medication logs |
| GET | `/api/parent/children/{child}/attendance` | Attendance history (`from_date`/`to_date`, default last 30 days) |
| GET | `/api/parent/children/{child}/daily-updates` | Daily updates (same date params) |
| GET | `/api/parent/invoices` | Parent's invoices with child + items |
| GET | `/api/parent/invoices/{invoice}` | Single invoice with child + items |

Child endpoints verify the child is linked to the authenticated parent via `child_parent` pivot — 403 if not. Invoice show verifies `parent_id === auth()->id()`.

**Carer** (`/api/carer/*` — requires `role=carer`):

| Method | URL | Description |
|---|---|---|
| GET | `/api/carer/rooms` | Carer's active room assignments with children |
| GET | `/api/carer/rooms/{room}/children` | Children in room with today's attendance status |
| POST | `/api/carer/attendance` | Mark attendance (updateOrCreate on child+date) |
| POST | `/api/carer/daily-updates` | Log daily update (updateOrCreate on child+date) |
| POST | `/api/carer/medication-logs` | Log medication administered |

Room endpoints verify carer is actively assigned (`activeRooms()`, end_date IS NULL) — 403 if not.

**Manager** (`/api/manager/*` — requires `role=manager`):

| Method | URL | Query params | Description |
|---|---|---|---|
| GET | `/api/manager/children` | `search`, `room_id` | Paginated children (15/page) with room + parents |
| GET | `/api/manager/parents` | `search` | Paginated parents with children count |
| GET | `/api/manager/carers` | `search`, `room_id` | Paginated carers with active rooms |
| GET | `/api/manager/rooms` | — | All rooms with children_count + active_carers_count |
| GET | `/api/manager/dashboard` | — | Counts + today's attendance + 5 recent invoices |

### Middleware

- `auth:sanctum` — Sanctum token guard (API routes)
- `api.role:X` — `ApiRoleMiddleware` (`app/Http/Middleware/ApiRoleMiddleware.php`), returns JSON 403 if role mismatch, 401 if no user

### Key files

- `routes/api.php` — all API route definitions
- `app/Http/Controllers/Api/ApiAuthController.php` — register, login, logout, user
- `app/Http/Controllers/Api/ParentDataController.php` — parent-scoped endpoints
- `app/Http/Controllers/Api/CarerDataController.php` — carer-scoped endpoints
- `app/Http/Controllers/Api/ManagerDataController.php` — manager-scoped endpoints
- `app/Http/Middleware/ApiRoleMiddleware.php` — role guard for API routes
- `config/sanctum.php` — Sanctum configuration
- `database/migrations/*_create_personal_access_tokens_table.php` — tokens table

### API Tests

- `tests/Feature/ApiAuthTest.php` — 9 tests: register, login, logout, token revocation, unauthenticated access
- `tests/Feature/ApiRoleGuardTest.php` — 10 tests: each role can access its own routes and is blocked (403) from other roles; unauthenticated gets 401

**Note on logout test:** After calling `/api/logout`, call `$this->app['auth']->forgetGuards()` before the follow-up request — otherwise Sanctum's guard caches the resolved user in memory and the revoked token still appears valid within the same test process.

### Messaging (Task #1602 — sprint-4)

`messages` table added via migration `2026_03_16_185253_create_messages_table.php`:

| Column | Type | Notes |
|---|---|---|
| `sender_id` | FK → users | cascade delete |
| `receiver_id` | FK → users | cascade delete |
| `child_id` | FK → children, nullable | null on delete — optional message context |
| `body` | text | required |
| `read_at` | timestamp, nullable | null = unread |

Indexes: `(sender_id, receiver_id)` for conversation lookups; `(receiver_id, read_at)` for unread queries.

- `app/Models/Message.php` — `sender()`, `receiver()`, `child()` relationships; `read_at` cast to datetime
- `User::sentMessages()` / `User::receivedMessages()` added to User model
