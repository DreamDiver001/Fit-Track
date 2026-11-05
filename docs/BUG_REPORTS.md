# FitTrack — Defect Reports

Defects were found while writing the test suite and reviewing the code. Each one has a regression test that fails on the old code and passes on the fix.

| ID | Title | Severity | Status |
|---|---|---|---|
| BUG-001 | Editing a workout wipes sets, reps and rest of all its exercises | High | Fixed |
| BUG-005 | Banned users keep access until their session expires | High | Fixed |
| BUG-008 | Weight-loss progress stuck at 0% if goal created without current value | Medium | Fixed |
| BUG-002 | `/admin/workouts/{id}` and `/admin/exercises/{id}` return HTTP 500 | Medium | Fixed |
| BUG-003 | Private workouts visible to anyone via direct URL | Medium | Fixed |
| BUG-006 | Unknown exercise ID in admin workout form causes HTTP 500 | Low | Fixed |
| BUG-004 | Users can log a private workout with a crafted request | Low | Fixed |
| BUG-007 | Workout logs accept dates in the future | Low | Fixed |

---

## BUG-001: Editing a workout wipes sets, reps and rest of all its exercises
**Severity:** High (silent data loss) · **Component:** `Admin\WorkoutController@update`

**Steps to reproduce**
1. Seed the database. "Push Day" has Bench Press 4×10 / 90 s, Overhead Press 4×10 / 90 s, Dumbbell Curl 3×12 / 60 s.
2. As admin, open *Edit* for "Push Day", change only the title, and save.
3. Open the public "Push Day" page.

**Expected:** Sets, reps, rest and exercise order are unchanged.

**Actual:** Every exercise shows "–" for sets, reps and rest. Exercises are re-ordered alphabetically.

**Root cause:** The edit form only sends `exercise_ids[]`. The controller rebuilt the pivot rows from `ex_sets.$i`, `ex_reps.$i` and `ex_rest.$i`, which are never sent, so it `sync()`-ed every exercise with `null` values. It also set `sort_order` to the checkbox position, which is alphabetical.

**Fix:** Moved the logic into `syncExercises()`. Values that are not submitted fall back to the existing pivot row. Existing exercises keep their `sort_order`, and newly added ones are appended at the end.

**Regression test:** `WorkoutManagementTest::editing_a_workout_keeps_existing_sets_reps_and_order`

---

## BUG-005: Banned users keep access until their session expires
**Severity:** High (access control) · **Component:** authentication

**Steps to reproduce**
1. Log in as `user@fittrack.com` in browser A.
2. In browser B, log in as admin and ban that user.
3. In browser A, navigate to `/dashboard` or create a log.

**Expected:** User A is logged out and told the account is suspended.

**Actual:** User A keeps full access until the session expires (120 minutes by default).

**Root cause:** `is_banned` was only checked inside `AuthenticatedSessionController@store`, which means only at the moment of login.

**Fix:** Added the `EnsureUserIsNotBanned` middleware to the `web` group (in `bootstrap/app.php`). It checks every request and logs out banned users.

**Regression test:** `AuthenticationTest::a_user_banned_during_their_session_is_logged_out_on_next_request`

---

## BUG-008: Weight-loss progress stuck at 0% if goal created without current value
**Severity:** Medium · **Component:** `GoalController@update`, `Goal::progress_percent`

**Steps to reproduce**
1. Create a *Weight Loss* goal with target 70 kg and leave *current value* empty.
2. Edit the goal and set current = 80. Edit again and set current = 75.

**Expected:** Progress shows 50% (5 of 10 kg lost).

**Actual:** Progress stays at 0% forever.

**Root cause:** `start_value` is copied from `current_value` only in `store()`. When it is `null`, the accessor uses `current_value` as the start, so "lost" is always 0.

**Fix:** In `update()`, record `start_value` from the first submitted current value if it is still `null`.

**Regression test:** `GoalTest::weight_loss_progress_works_when_the_goal_was_created_without_a_current_value`

---

## BUG-002: `/admin/workouts/{id}` and `/admin/exercises/{id}` return HTTP 500
**Severity:** Medium · **Component:** `routes/web.php`

**Steps to reproduce:** As admin, open `/admin/workouts/1`.

**Expected:** 404 or 405.

**Actual:** HTTP 500, `Method Admin\WorkoutController::show does not exist`.

**Root cause:** `Route::resource()` registers a `show` route, but neither admin controller implements `show()`.

**Fix:** Added `->except(['show'])` to both admin resource routes.

**Regression test:** `AdminAccessTest::admin_resource_urls_without_a_page_do_not_crash`

---

## BUG-003: Private workouts visible to anyone via direct URL
**Severity:** Medium (information disclosure) · **Component:** `WorkoutController@show`

**Steps to reproduce:** As admin, untick "Make this workout public" on a workout. Log out and open `/workouts/{id}`.

**Expected:** 404. The listing pages already hide private workouts.

**Actual:** The full workout page is shown.

**Root cause:** `index()` filters `is_public = true`, but `show()` used route-model binding with no visibility check.

**Fix:** `abort_if(!$workout->is_public && !auth()->user()?->is_admin, 404)`. Admins can still preview.

**Regression test:** `PublicPagesTest::a_private_workout_is_not_visible_by_direct_url`

---

## BUG-006: Unknown exercise ID in admin workout form causes HTTP 500
**Severity:** Low · **Component:** `Admin\WorkoutController@store/update`

**Steps to reproduce:** Submit the create-workout form with `exercise_ids[]=99999` (edited HTML or a deleted exercise in another tab).

**Expected:** Validation error.

**Actual:** HTTP 500, `SQLSTATE[23000]: FOREIGN KEY constraint failed`. The workout row was already saved before the crash.

**Root cause:** `exercise_ids`, `ex_sets`, `ex_reps` and `ex_rest` were read from the request without validation.

**Fix:** Added rules: `exercise_ids.*` must be a distinct existing exercise ID, and sets, reps and rest must be non-negative integers.

**Regression test:** `WorkoutManagementTest::an_unknown_exercise_id_is_a_validation_error_not_a_crash`

---

## BUG-004: Users can log a private workout with a crafted request
**Severity:** Low · **Component:** `WorkoutLogController`

**Steps to reproduce:** POST `/logs` with the ID of a private workout.

**Expected:** Validation error. The form only offers public workouts.

**Actual:** The log is saved.

**Root cause:** The validation rule was `exists:workouts,id`, which accepts any workout.

**Fix:** Changed it to `Rule::exists('workouts','id')->where('is_public', true)`. The duplicated store/update rules were also merged into one `rules()` method.

**Regression test:** `WorkoutLogTest::a_private_workout_cannot_be_logged`

---

## BUG-007: Workout logs accept dates in the future
**Severity:** Low · **Component:** `WorkoutLogController`

**Steps to reproduce:** Log a workout with tomorrow's date.

**Expected:** Validation error, because a log records a completed workout.

**Actual:** Saved. Totals on the dashboard include workouts that haven't happened.

**Fix:** `logged_at` now uses `required|date|before_or_equal:today`.

**Regression test:** `WorkoutLogTest::a_workout_cannot_be_logged_in_the_future`
