# FitTrack — Test Plan

| | |
|---|---|
| **Application** | FitTrack (Laravel 11, SQLite) — workout logging, goals, admin back-office |
| **Version under test** | `main` branch |
| **Author** | Mohamed Aziz Zarraa |

## 1. Objective
Verify that FitTrack's core features work as specified, that users cannot read or change each other's data, that admin functions are restricted to admins, and that invalid input is rejected with a clear error instead of crashing the application.

## 2. Scope

**In scope**
- Authentication: register, login, logout, banned accounts
- Authorization: route protection, admin middleware, `WorkoutLogPolicy`, `GoalPolicy`
- Workout logs CRUD (N side of `users → workout_logs`)
- Goals CRUD and progress calculation
- Public catalogue: workouts and exercises listing, filtering, search, detail pages
- Dashboard statistics and workout streak
- Admin: user ban/unban/delete, workouts CRUD with exercise assignment (N–N pivot), exercises CRUD with image upload

**Out of scope**
- Visual layout and responsiveness (covered only by manual exploratory checks)
- Performance and load testing
- Third-party CDN assets (Tailwind / DaisyUI)

## 3. Test levels and approach

| Level | Tool | What it covers |
|---|---|---|
| **Unit** | PHPUnit | Model logic in isolation: goal progress %, label accessors, tag parsing, policies |
| **Feature / integration** | PHPUnit + Laravel HTTP testing, in-memory SQLite | Full request → controller → database → response cycle for every route |
| **Regression** | PHPUnit | One automated test per fixed defect (marked `BUG-00X regression test`) |
| **Manual** | Test case table in [MANUAL_TEST_CASES.md](MANUAL_TEST_CASES.md) | UI flows, file upload UX, messages shown to the user |
| **Continuous integration** | GitHub Actions | Whole suite runs on every push and pull request |

### Test design techniques
- **Equivalence partitioning:** valid / invalid classes for every validated field (for example, effort rating ∈ {great, okay, tough} vs. anything else).
- **Boundary value analysis:**
  - Duration: 0 ✗, 1 ✓, 600 ✓, 601 ✗
  - Upload size: 2048 KB ✓, 2049 KB ✗
  - Password length: 7 ✗
  - Goal deadline: today ✗, tomorrow ✓
  - Log date: today ✓, tomorrow ✗
- **Decision table:** access matrix of guest / user / admin × each protected route.
- **State transition:** goal `in_progress → completed`; user `active ⇄ banned`.
- **Negative testing:** spoofed `user_id`, other users' resource IDs, unknown foreign keys, non-image uploads.

## 4. Test environment
- PHP 8.4, Composer, SQLite in-memory database (`phpunit.xml`)
- Fresh database per test (`RefreshDatabase`), fake file storage (`Storage::fake`)
- Test data generated with model factories in `database/factories`

## 5. Entry and exit criteria
**Entry:** the application installs with `composer install` and migrations run cleanly.

**Exit:**
- All automated tests pass in CI.
- All high-severity defects are fixed and covered by a regression test.
- Any open low-severity defects are documented in [BUG_REPORTS.md](BUG_REPORTS.md).

## 6. Defect management
Defects are logged with an ID, severity, steps to reproduce, expected vs. actual result, root cause and fix. They are tracked in [BUG_REPORTS.md](BUG_REPORTS.md) and as GitHub Issues.

| Severity | Meaning |
|---|---|
| High | Data loss, security or access-control failure |
| Medium | Feature broken or server error (500) on a reachable URL |
| Low | Missing validation or inconsistent behaviour with a workaround |

## 7. Risks
| Risk | Mitigation |
|---|---|
| Users accessing other users' data | Policy tests for every edit/update/delete route |
| Admin pages exposed | Access-matrix tests (guest / user / admin) |
| Pivot data (sets, reps) silently lost | Regression test BUG-001 |
| Uploaded files left orphaned | Tests assert old files are deleted on replace/delete |
