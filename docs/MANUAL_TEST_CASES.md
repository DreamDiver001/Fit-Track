# FitTrack — Manual Test Cases

**Setup:** `php artisan migrate:fresh --seed`, then `php artisan serve`.

| Account | Email | Password |
|---|---|---|
| Admin | `admin@fittrack.com` | `password` |
| User | `user@fittrack.com` | `password` |

**Status values:** ✅ Pass · ❌ Fail (link the bug ID) · ⏸ Blocked

| ID    | Title                                 | Status |
| ----- | ------------------------------------- | ------ |
| TC-01 | Register a new account                | ✅ Pass |
| TC-02 | Register with existing email          | ✅ Pass |
| TC-03 | Login with wrong password             | ✅ Pass |
| TC-04 | Banned user cannot log in             | ✅ Pass |
| TC-05 | User banned mid-session is logged out | ✅ Pass |
| TC-06 | Log a workout with all input types    | ✅ Pass |
| TC-07 | Duration boundaries                   | ✅ Pass |
| TC-08 | Future date rejected                  | ✅ Pass |
| TC-09 | Edit and delete own log               | ✅ Pass |
| TC-10 | Cannot edit another user's log        | ✅ Pass |
| TC-11 | Weight-loss progress                  | ✅ Pass |
| TC-12 | Deadline must be in future            | ✅ Pass |
| TC-13 | Filter workouts                       | ✅ Pass |
| TC-14 | Private workout hidden                | ✅ Pass |
| TC-15 | Non-admin blocked                     | ✅ Pass |
| TC-16 | Create exercise with image            | ✅ Pass |
| TC-17 | Reject non-image upload               | ✅ Pass |
| TC-18 | Edit workout keeps sets/reps          | ✅ Pass |
| TC-19 | Ban / unban user                      | ✅ Pass |
| TC-20 | Admin cannot be deleted               | ✅ Pass |
| TC-21 | Streak counter                        | ✅ Pass |

Test execution summary:

- Total test cases: 21
- Passed: 21
- Failed: 0
- Blocked: 0
- Success rate: 100%

All 21 manual test cases were executed successfully. The application behaved as expected across authentication, workout logging, goal tracking, catalogue filtering, security, administration, and dashboard functionality. No failures or blocking issues were identified.

