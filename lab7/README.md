# Lab 7 Peer Tutoring Hub

Generated classroom application extending the supplied lab6.zip. Uses procedural PHP, PDO, SQLite, and the existing three tiers: pages/controllers, services, and repositories. Requires PHP 8 with PDO SQLite and mbstring. Only fictional test accounts belong in this application.

## First checks in Codespaces

From the repository root, after uploading the lab7 folder:

```bash
cd lab7
php -v
php -m
find . -name '*.php' -print0 | xargs -0 -n1 php -l
```

Confirm pdo_sqlite and mbstring are available. No PHP runtime exists on the generating computer, so PHP lint, PHP tests, and browser behavior have NOT been verified there. Inspect and execute this package in Codespaces before treating it as working or submitting it.

## Database setup

Start with this new lab7 directory; do not run these against the Lab 6 working database. The package intentionally contains no existing tutoring.db.

```bash
cd database
sqlite3 -bail tutoring.db < setup-database.sql
sqlite3 -bail tutoring.db < upgrade-to-user-management.sql
sqlite3 tutoring.db ".tables"
php create-admin.php "Test Administrator" admin@example.com "TestPassword123"
cd ..
```

The administrator command uses a disposable example password. Do not put an actual password in your submitted notes. Run the setup and upgrade ONCE on the new Lab 7 database. The upgrade adds a column and is not rerunnable. The original setup script retains its classroom seed student and request. Rerunning it can duplicate the sample request.

## Run

From lab7:

```bash
php -S 0.0.0.0:8000 php/router.php
```

Open forwarded port 8000. This adds the existing router to the assignment's server command so database files, source documents, and internal folders cannot be downloaded. Keep port visibility Private. The starting page redirects through profile.php to login.php for a visitor without a session.

Register student@example.com as a Student with fictional values, then test profile editing, requesting a session, editing an Open request, viewing My requests, and logging out. Log in with the CLI-created administrator to inspect users and all requests. Record the actual results and screenshots in lab7-notes.md.

## Requirements and implementation

| Use case | Page | Service or helper | Repository |
|---|---|---|---|
| UM-01 Create account | php/register.php | registerUser / createAccount | insertUser / insertLinkedStudent |
| UM-02 Own profile | php/profile.php | requireLogin | findUserById |
| UM-03 Search users | php/users.php and php/manage-user.php | requireLogin / requireRole | listUsers / findUserById |
| UM-04 Update own profile | php/profile.php | saveOwnProfile | updateUserProfile / updateLinkedStudent |
| UM-05 Change password | php/change-password.php | changeOwnPassword | updatePasswordHash |
| UM-06 Update role | php/manage-user.php | manageUserAccess | updateUserAccess |
| UM-07 Account status | php/manage-user.php | manageUserAccess | updateUserAccess |
| UM-08 Delete account | php/manage-user.php | deleteUserAccount blocks deletion pending policy | findUserById; no delete query |
| UM-09 Login | php/login.php | authenticateUser / establishLogin | findUserByEmail |
| UM-10 Logout | php/logout.php | verifyCsrfToken / logoutSession | No database write |

The page handles its own POST for account workflows. Separate registration and login controllers are not necessary: the POST-handling section runs before HTML in register.php or login.php. Request controllers remain separate as in Lab 6.

## Security and scope decisions

- Public registration accepts Student or Tutor, never Administrator. The assignment walkthrough requires Student and Administrator. Tutors can manage their own account but receive 403 on request pages.
- Passwords use password_hash and password_verify in services. The minimum is 12 characters from the lab. The additional maximum is 72 bytes to avoid bcrypt truncation. PHP generates salts.
- A protected request checks login, a 20-minute inactivity timeout, and current Active status and role reloaded from SQLite. Login regenerates the session ID. Cookies use HttpOnly and SameSite=Lax; Secure is enabled for HTTPS or the Codespaces forwarding environment.
- State changes require POST and a matching session CSRF token. Profile IDs come from the authenticated account. Student request identity comes from that account even if posted values are tampered with.
- Student reads and writes restrict students.user_id in SQL. Administrators see all requests and may edit/cancel Open requests, but cannot submit student requests.
- users connects to students through a unique nullable students.user_id. Existing seed records remain unlinked. Registration does not claim legacy records merely by matching an unverified email. Use a different fictional email if it conflicts with a legacy record. Future migration/account-claim verification is outside this lab.
- Student name/email stay synchronized during profile edits. Changing another role to Student creates a linked student profile if needed. Existing tutoring records survive later role changes.
- The design leaves permanent deletion and retention unresolved. UM-08 shows a confirmation and denies deletion, explaining why. Deactivation is available. The last active administrator cannot be demoted or deactivated.
- Original Lab 6 functions remain for the supplied regression tests. HTTP controllers use authenticated workflows and scoped repository queries. Internal PHP files and tests are not web routes.
- This is a classroom demonstration, not a production service. Email verification, password recovery, rate limiting, global revocation of other sessions after a password change, and production deployment configuration are outside this package.

## Automated tests

From lab7:

```bash
php tests/validation-tests.php
php tests/user-management-tests.php
php tests/integration-tests.php
```

The two supplied Lab 6 test files are preserved byte-for-byte. validation-tests.php prints HTML paragraphs even in the terminal; paste its complete output. Its exit status alone does not prove all checks passed, so inspect every PASS/FAIL label.

user-management-tests.php is new and uses only sqlite::memory:. It covers account validation, password changes, role/status operations, blocked deletion, student ownership reads and writes, authenticated request identity, session regeneration, CSRF token comparison, and clearing the server session. It does not prove HTTP redirects/status codes, browser cookie expiry, inactivity handling, direct-URL guards, or visual layout. Verify those manually in the running app.

Source inspection predicts three inherited integration failures: Duplicate email reuses student, UC02 course and status filter, and Invalid submission writes nothing. The supplied setup script seeds one student and one Open IS 6465 request, while these assertions expect counts for an empty database. This is a prediction, not executed Lab 7 output. Run the original tests, record the actual full output, and investigate any difference. Do not change supplied tests to hide failures.

## Manual security checks

Use separate fictional student accounts for owner tests. Record expected and actual results:

| Action | Expected |
|---|---|
| Signed-out visitor opens php/requests.php | Redirect to login |
| Student opens php/users.php or php/manage-user.php | 403 |
| Tutor opens a request page | 403 |
| Student A opens Student B's request ID | 404, no record disclosed |
| Student A posts an update/cancellation for Student B's ID with A's valid CSRF token | 404, no change |
| State-changing request omits or changes csrf_token | 403, no change |
| Inactive account attempts login | Generic login rejection |
| Account is deactivated while already logged in | Next protected request ends session |
| Session is idle for more than 20 minutes | Redirect to login with expired indicator |
| Logged-out visitor revisits a protected URL | Redirect to login |
| Browser requests /database/tutoring.db with router enabled | 404 |

## Generation checks actually completed locally

- SQLite setup and upgrade executed successfully using Python's SQLite in a disposable in-memory database.
- The three required tables appeared; the account link enforced uniqueness and foreign keys; PRAGMA foreign_key_check returned no violations.
- Supplied validation-tests.php and integration-tests.php were compared byte-for-byte with lab6.zip and are unchanged.
- Literal PHP include paths were checked for existing targets.
- These are source/schema checks, not PHP execution or Codespaces test results.
