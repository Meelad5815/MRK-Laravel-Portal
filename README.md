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

Keep these values private. The workflow intentionally stops before deployment if the required application/database secrets are missing. Database credentials and FTP details cannot safely be guessed; get them from the hosting control panel. The deployment workflow uploads the app and then calls a protected migration endpoint, so the hosting platform must permit automated HTTP requests to reach PHP.

## Hosting availability and migration troubleshooting

The automated monitor validates the response body, not just HTTP status. InfinityFree may return HTTP 200 with a JavaScript/AES browser-verification page instead of the requested file. If logs contain `slowAES`, `__test=`, or `HOSTING_BOT_CHALLENGE`, the runner has not reached the actual website/PHP script. A browser-like user-agent alone does not bypass this challenge.

If deployment logs report `Production migration is NOT verified`, do not treat the database migration as successful. The protected endpoint must return the exact Laravel success marker; an HTTP 200 challenge page is not sufficient. The temporary migration endpoint is removed by the workflow cleanup step even when the migration check fails.

**Important: this is a known limitation of InfinityFree free hosting, not a Laravel bug.** InfinityFree's published browser-security policy says the JavaScript/cookie challenge is mandatory on free accounts, cannot be disabled, and blocks cURL and other automated HTTP clients. A support request cannot create an exception for this on the free plan. See the [official browser-security policy](https://forum.infinityfree.com/t/browser-security-system-features-and-limitations/49353) and [official support options](https://www.infinityfree.com/contact/).

Available paths:
1. **Recommended for automatic Laravel deployment:** move this application to a host that supports automated HTTP requests and Laravel production commands (SSH/CLI), then configure the new hosting credentials in GitHub Actions.
2. **Stay on InfinityFree:** FTP upload may work, but automated monitoring and the protected HTTP migration step will remain blocked by the browser challenge. Database migrations must be handled manually through a supported database tool such as phpMyAdmin, after reviewing/backing up the database and applying the migration SQL carefully. Do not assume migrations ran just because FTP upload succeeded.
3. Check the InfinityFree Client Area for account/domain status and use the community forum for questions, but do not expect the mandatory free-plan challenge to be disabled.

Do not expose the deployment key in a URL or remove the migration endpoint's authentication. The deployment is only complete when the migration success marker is verified.

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
