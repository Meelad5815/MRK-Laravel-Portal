# MRK Digital

Professional Laravel portal for MRK Digital / Online Services Center.

## Automatic delivery

GitHub Actions tests, builds and deploys the production package to InfinityFree when `main` changes.

## Configure production deployment (required)

Before the first successful deployment, add these **repository Actions secrets** in GitHub: **Settings → Secrets and variables → Actions → New repository secret**.

| Secret name | Value |
| --- | --- |
| `APP_KEY` | A persistent Laravel application key. Generate it locally with `php artisan key:generate --show` and paste the output directly into the secret. Never commit it or post it in chat. Do not change it on each deployment. |
| `APP_URL` | The site's HTTPS base URL: `https://meeladrazakhokhar786.freehosting.dev` |
| `DB_HOST` | The MySQL hostname shown in the InfinityFree control panel under MySQL Databases. |
| `DB_DATABASE` | The full database name shown in that panel. |
| `DB_USERNAME` | The database username shown in that panel. |
| `DB_PASSWORD` | The database password for that database. |
| `FTP_USERNAME` | The FTP username shown in the InfinityFree account/control panel. |
| `FTP_PASSWORD` | The matching FTP password. |

Keep these values private. The workflow intentionally stops before deployment if the required application/database secrets are missing. Database credentials and FTP details cannot safely be guessed; get them from the hosting control panel. The deployment workflow uploads the app and then calls a protected migration endpoint, so the hosting account must also be reachable for deployment to complete.

## Hosting availability and migration troubleshooting

The automated monitor validates the response body, not just HTTP status. InfinityFree may return HTTP 200 with a JavaScript/AES browser-verification page instead of the requested file. If logs contain `slowAES`, `__test=`, or `HOSTING_BOT_CHALLENGE`, the runner has not reached the actual website/PHP script. A browser-like user-agent alone does not bypass this challenge.

If deployment logs report `Production migration is NOT verified`, do not treat the database migration as successful. The protected endpoint must return the exact Laravel success marker; an HTTP 200 challenge page is not sufficient. The temporary migration endpoint is removed by the workflow cleanup step even when the migration check fails.

To resolve this on InfinityFree:
1. Open the site in a normal browser and complete any hosting verification page.
2. Sign in to the InfinityFree Client Area and check the hosting account/domain status and any security/abuse restrictions.
3. Review the hosting PHP error log and verify PHP 8.2 compatibility and the configured document root.
4. If the browser-verification challenge is applied to all requests, ask InfinityFree support whether automated requests can be exempted or whether the challenge can be disabled for the site. Do not expose the deployment key in a URL or remove the endpoint's authentication.
5. Re-run the GitHub Actions deployment after the hosting layer allows the protected PHP endpoint to execute. The deployment is only complete when the migration success marker is verified.

## Planned platform modules

Services, projects/portfolio, customer leads, digital online services, automation projects, documents/forms, integrations and future AI-assisted workflows can be added without changing the core Laravel architecture.

## Architecture

MRK Laravel Portal is the business-management/backend application for MRK Digital Center. The public WordPress site can handle SEO, content, services and lead acquisition, while this Laravel application manages operational data such as leads, customers, projects, quotations, invoices and blog content.

## Local development

Requirements: PHP 8.2+, Composer, Node.js 20+, and SQLite or MySQL.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan test
```

For development, use `composer run dev`.

## Security

Never commit `.env`, database credentials, FTP credentials, API tokens, OAuth secrets, or other private configuration. Production secrets must be supplied through GitHub Actions secrets and the hosting environment.

## CI

GitHub Actions runs Composer installation, Laravel migrations, the frontend build, and the automated test suite on pushes and pull requests targeting `main`.
