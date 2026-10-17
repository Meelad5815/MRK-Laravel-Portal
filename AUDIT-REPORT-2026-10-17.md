# MRK Laravel Portal — Repository Audit
**Audit date:** 2026-10-17  
**Repository:** `Meelad5815/MRK-Laravel-Portal`  
**Scope:** Source structure, migrations, deployment workflow, CI results, authentication, public routes, SEO and shared-hosting deployment.

## Executive summary
The application passes the latest Laravel test and CI workflows, but the production deployment is **not verified**. FTP upload succeeds; the deployment workflow fails while trying to run database migrations over HTTP because InfinityFree returns its JavaScript/AES bot-challenge page instead of executing the temporary migration endpoint. The production database schema and data state therefore remain unknown.

## Findings

### P0 — Production database migrations are not verified
- Latest deploy run `39041850682` failed at “Diagnose and run production database migrations”.
- The response body is an InfinityFree JavaScript/AES challenge even though the HTTP status is 200.
- The workflow correctly refuses to call the deployment complete, but it currently cannot finish database setup unattended.
- The temporary endpoint is removed by cleanup after failure, so the failed run does not leave a migration endpoint available.

**Impact:** If the live database is missing required tables, pages that query `services`, `site_settings`, `blog_posts`, `content_pages`, customers, quotes or invoices can return HTTP 500.

**Remediation:** Use a verified database migration path compatible with the host. First back up the current database and inspect its table/migration state. Do not run `migrate:fresh` or import schema SQL blindly on the existing database.

### P1 — The phpMyAdmin SQL bootstrap is incomplete
- `database/infinityfree.sql` creates only a subset of the application's tables.
- It does not create all tables used by the current code, including services, customers, quotes, invoices, site settings, blog posts and content pages.
- It records only a subset of migration names. Importing it alone is not a complete installation for the current application.

**Impact:** Importing this file alone cannot guarantee a working website.

**Remediation:** Replace it with a complete, version-matched bootstrap procedure only after backing up and checking the existing database. Keep migration history consistent with the actual schema.

### P1 — Public pages depend on database availability
- The homepage queries published featured services.
- The shared layout queries `site_settings` on each rendered page.
- Blog, resources and service pages also query their respective content tables.

**Impact:** Missing or inaccessible tables can cause public pages to fail rather than degrade gracefully.

**Remediation:** Complete the production migrations first; then add targeted tests for database-unavailable and partially initialized states if graceful degradation is desired.

### P2 — Monitoring results are not equivalent to a browser health check
- The scheduled monitor uses `curl` against static and Laravel URLs.
- InfinityFree's JavaScript/AES challenge can make a legitimate browser site look unavailable to non-browser probes.

**Impact:** Monitor alerts may reflect anti-bot interception rather than the actual Laravel response.

**Remediation:** Distinguish host challenge responses from real Laravel HTTP errors; verify the public site in a normal browser and use host-side PHP error logs for actual exceptions.

### P2 — Duplicate/legacy CI workflow exists
- `ci-check.yml` and `laravel-tests.yml` are active validation workflows.
- `ci.yml` is retained as a manual legacy workflow.

**Impact:** More than one workflow can perform similar validation, increasing maintenance overhead.

**Remediation:** Keep the active workflows documented and disable/remove the legacy workflow only after confirming no manual process depends on it.

## Positive checks
- Latest Laravel Tests run: successful.
- Latest Laravel CI Check run: successful.
- Deployment FTP sync: successful.
- User model casts `is_admin` to boolean; the admin middleware checks the typed boolean.
- Login and registration requests have throttling; public enquiry requests are throttled.
- The migration deployment endpoint requires an authenticated secret header and is not intended as a public unauthenticated action.
- Public routes include robots.txt and sitemap.xml endpoints.
- The repository includes feature tests for authentication/admin access, services, business records and content catalogue.

## Verification still required
1. Back up the current production database.
2. Inspect the live `migrations` table and actual table/column list in phpMyAdmin.
3. Apply the missing migrations through a host-compatible, verified method.
4. Confirm the homepage, services, blog, resources, login and admin dashboard in a normal browser.
5. Review InfinityFree PHP error logs if any route still returns 500.
6. Re-run deployment and health monitoring after the database state is verified.

## Safety note
Never publish `.env`, `APP_KEY`, database credentials, FTP credentials, passwords or customer records in issues, commits or screenshots. Never use `migrate:fresh` against production data.
