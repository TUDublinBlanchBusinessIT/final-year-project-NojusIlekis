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
  ↕ (pivot: room_user)
Room
  ↑ Child (room_id, parent_user_id → User)
      ↑ Attendance (child_id, date, status: present|absent, room_id, recorded_by)
      ↑ DailyReport (child_id, carer_id, date, daily_report text)
      |     ↑ MediaUpdate (daily_report_id)
      ↑ DailyUpdate (child_id, date, meals, sleep, notes, created_by)
      ↑ MedicationLog (child_id, carer_id, medication_name, dosage, date, time_given, notes)

Acknowledgement (record_type, record_id, parent_id, status: pending|acknowledged, signed_at, signature_name)
```

Carers are assigned to rooms via the `room_user` pivot table. Attendance records store `room_id` directly (denormalised) to support efficient room-level filtering.

### Controller Structure

```
app/Http/Controllers/
  Carer/
    AttendanceController    — mark children present/absent by room and date
    DailyReportController   — write/view written reports + media uploads per child
    DailyUpdateController   — structured updates (meals, sleep, notes) per child
    MedicationController    — log medication administered to children (scoped to carer's rooms)
  Manager/
    DashboardController     — attendance KPI dashboard with date/room filters and trend chart
    ReportsController       — attendance and task reports
    DailyReportsController  — view carer-written reports; request parent acknowledgement
    MedicationLogsController — view all medication logs filtered by date/room
  Parent/
    AcknowledgementController — list pending acknowledgements; sign with name + checkbox
  ProfileController         — shared profile edit/delete
```

### Frontend

Blade templates in `resources/views/`, organised by role (`carer/`, `manager/`, `dashboards/`). Layouts are in `resources/views/layouts/` (`app.blade.php` for authenticated, `guest.blade.php` for auth screens). Alpine.js handles interactive UI behaviour inline. Charts on the manager dashboard use Chart.js data injected from Blade.

### Testing

Tests use Pest PHP with an in-memory SQLite database (configured in `phpunit.xml`). The `tests/Feature/` directory contains role-access tests and auth tests. Use `User::factory()->create(['role' => 'carer'])` pattern to create role-specific users in tests.

## Seed Test Accounts

All seed users have password `Password123!`:

| Email | Role |
|---|---|
| parent@test.com | parent |
| carer@test.com | carer |
| manager@test.com | manager |
| parent2@test.com | parent |

Seed rooms: **Bumblebees** and **Ladybirds**. The carer is assigned to both rooms.
