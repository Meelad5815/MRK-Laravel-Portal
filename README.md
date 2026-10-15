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

## Hosting availability

The automated monitor checks the public site and static/PHP probes. If it reports `curl: (52) Empty reply from server` for all probes, even static files, this indicates the host/web layer is not returning HTTP responses; application code changes alone cannot resolve that. Check the InfinityFree account/domain status and hosting service, then rerun the monitor.

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
