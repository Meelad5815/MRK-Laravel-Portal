# Deploy MRK Digital Laravel on Render (free test setup)

This repository now includes a Dockerfile and Render Blueprint for a Laravel 12 / PHP 8.3 deployment.

## Important before switching
- This creates a **new application deployment and a new PostgreSQL database connection**. It does not copy existing InfinityFree MySQL data.
- Do not delete the InfinityFree site or database until you have exported and verified a backup.
- Render's free web service can sleep when idle and has usage limits; treat it as a test/hobby deployment, not guaranteed always-on production hosting.
- The app runs Laravel migrations on container startup. Point `DB_URL` only at a new/verified database, never at an unrelated database.

## Steps requiring account access
1. Create a free PostgreSQL project at https://neon.tech/ .
2. In Neon, open **Connect**, select the database and role, and copy the PostgreSQL connection string. Keep `sslmode=require` in the URL. Do not paste the URL into GitHub files or chat.
3. Open https://dashboard.render.com/blueprints and create a new Blueprint from this public repository:
   `https://github.com/Meelad5815/MRK-Laravel-Portal`
4. Render reads `render.yaml`. When prompted, set:
   - `DB_URL`: the Neon PostgreSQL connection string
   - `APP_URL`: the final Render service URL (for example, the actual `.onrender.com` URL Render assigns; do not guess it)
5. Let the Docker build and deploy finish. The Blueprint generates `APP_KEY` for the new Render service.
6. Open the assigned Render URL and check the home page, `/tools`, `/sitemap.xml`, and `/up`. Review Render deploy logs if the health check fails.
7. Keep the old site and database until the new deployment and any required data transfer have been verified.

## What's configured
- Docker image with PHP 8.3, PostgreSQL PDO support, Composer and Vite asset build.
- Production environment, Laravel health check at `/up`, and environment variables kept out of source control.
- Startup migrations run with `php artisan migrate --force`.

## Remaining manual work
I cannot create projects inside your Render/Neon accounts or see their private credentials from GitHub. You must connect the accounts and supply the private database URL through Render's secret/environment-variable form. If the old MySQL database contains important customers, leads, invoices, users, or content, export it and plan a schema/data migration before switching traffic.
