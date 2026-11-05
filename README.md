# FitTrack — Fitness & Workout Tracker

![Tests](https://github.com/DreamDiver001/fittrack/actions/workflows/tests.yml/badge.svg)

Laravel 11 web app for logging workouts, tracking goals and managing a workout/exercise catalogue, with an admin back-office.

## Testing & QA

| | |
|---|---|
| Automated tests | **74 test methods / 93 cases** (PHPUnit): 3 unit + 9 feature test classes |
| Test levels | Unit (model logic, policies) · Feature/integration (every route, DB, file uploads) · Regression |
| Techniques | Equivalence partitioning, boundary values, access-control decision table, negative tests |
| CI | GitHub Actions runs the suite with coverage on every push and pull request |
| Defects found | **8**, all root-caused, fixed and covered by a regression test |

**Documentation**
- [Test plan](docs/TEST_PLAN.md) — scope, approach, entry/exit criteria, risks
- [Manual test cases](docs/MANUAL_TEST_CASES.md) — 21 cases with preconditions, steps and expected results
- [Defect reports](docs/BUG_REPORTS.md) — reproduction steps, root cause and fix for each bug

### Run the tests
```bash
composer install
cp .env.example .env && php artisan key:generate
vendor/bin/phpunit --testdox
```

```
tests/
├── Unit/                     GoalProgressTest, ModelAccessorsTest, PolicyTest
└── Feature/
    ├── AuthenticationTest, PublicPagesTest, WorkoutLogTest, GoalTest, DashboardTest
    └── Admin/                AdminAccessTest, UserManagementTest, ExerciseManagementTest, WorkoutManagementTest
```

---

# AWP Server-side 



### Accounts
- **Admin:** admin@fittrack.com / password

---


### Database Requirements

- [x] At least 5 database tables with meaningful relationships and clear responsibilities
  - `users`, `workouts`, `exercises`, `workout_exercises` (pivot), `workout_logs`, `goals` — **6 tables**
- [x] At least one 1-to-N (one-to-many) relationship implemented and actively used
  - `users → workout_logs`, `users → goals`, `workouts → workout_logs`
- [x] At least one N-to-N (many-to-many) relationship with a proper pivot table
  - `workouts ↔ exercises` via `workout_exercises` pivot table (with sets, reps, rest_seconds, sort_order)
- [x] Consistent use of primary keys, foreign keys, and unique constraints
  - All tables use auto-increment PKs, FKs with cascadeOnDelete, and unique constraint on `(workout_id, exercise_id)`
- [x] At least one table with 10 or more fields (complex data modeling)
  - `goals` has 12 fields; `workout_logs` has 10 fields
- [x] Schema includes various data types:
  - [x] `integer` — ids, duration_minutes, calories_burned, sets, reps, rest_seconds
  - [x] `boolean` — is_admin, is_banned, is_public
  - [x] `enum` — category, difficulty, muscle_group, equipment, effort_rating, goal_type, status
  - [x] `timestamp` — created_at, updated_at, email_verified_at
  - [x] `date` — logged_at (workout_logs), deadline (goals)

### Frontend Pages

- [x] Responsive, visually appealing UI (usable on desktop and mobile)
  - Tailwind CSS + DaisyUI via CDN, responsive grid layouts on all pages
- [x] Use of a component library (Bootstrap, Tailwind, Flowbite, etc.)
  - **DaisyUI v4** + **Tailwind CSS** used throughout
- [x] At least 10 distinct pages with unique functionality or content
  1. Home `/` — hero, featured workouts, feature highlights
  2. Workouts listing `/workouts` — search + filter by category/difficulty
  3. Workout detail `/workouts/{id}` — exercise table, stats
  4. Exercise library `/exercises` — search + filter by muscle group/equipment
  5. Login `/login` — authentication form
  6. Register `/register` — registration form
  7. Dashboard `/dashboard` — personal stats, recent logs, goal progress
  8. Workout Logs `/logs` — paginated log history
  9. Log Workout `/logs/create` — full form with all input types
  10. My Goals `/goals` — goal cards with progress bars
  11. Create Goal `/goals/create` — goal creation form
  12. Admin Dashboard `/admin` — system stats + quick actions
  13. Admin User Management `/admin/users` — ban/unban/delete users
  14. Admin Workout Management `/admin/workouts` — CRUD table
  15. Admin Exercise Management `/admin/exercises` — CRUD table (N side of M2M)

### CRUD Functionality

- [x] CRUD implemented for an entity on the "N" side of a one-to-many relationship
  - **WorkoutLog** (N side of `users → workout_logs`): Create `/logs/create`, Read `/logs`, Update `/logs/{id}/edit`, Delete (form POST)
- [x] CRUD implemented for an entity on the "N" side of a many-to-many relationship
  - **Exercise** (N side of `workouts ↔ exercises`): Create `/admin/exercises/create`, Read `/admin/exercises`, Update `/admin/exercises/{id}/edit`, Delete (form POST)
- [x] Forms include multiple input types:
  - [x] **Checkbox list** — Tags in Log Workout form (`tags[]`)
  - [x] **Radio buttons** — Effort rating in Log Workout form (`effort_rating`)
  - [x] **Select dropdown** — Workout selector, Category, Difficulty, Goal Type, Muscle Group, Equipment
  - [x] **File upload component** — Exercise image in Admin Create/Edit Exercise; Workout image in Admin Create/Edit Workout

### Authentication & Authorization

- [x] Full authentication and authorization using Laravel's built-in tools
  - Laravel Breeze-style auth with `Auth::attempt`, `Auth::login`, session regeneration
- [x] Logged-in users cannot access protected data belonging to other users
  - `WorkoutLogPolicy` and `GoalPolicy` enforce `user_id === auth()->id()` before update/delete
  - All queries scoped to `where('user_id', auth()->id())`
- [x] Sensitive actions must be controlled by roles or permissions
  - `AdminMiddleware` gates all `/admin/*` routes — returns 403 for non-admins
  - Banned users are prevented from logging in

### Admin Interfaces

- [x] Administrator user-management page(s)
  - `/admin/users` — view all users, ban/unban, delete (protected admins cannot be deleted)
- [x] Full CRUD management for at least one non-user entity
  - `/admin/exercises` — full CRUD for exercises (the N side of the M2M relationship)
  - `/admin/workouts` — full CRUD for workouts including exercise assignment

### Technology Requirements

- [x] Laravel (v11)
- [x] Laravel Breeze-style authentication scaffolding (manual implementation matching Breeze conventions)
- [x] SQLite as the database engine (`DB_CONNECTION=sqlite`)
