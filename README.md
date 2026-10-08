# MRK Digital

Professional Laravel portal for MRK Digital / Online Services Center.

## Automatic delivery
GitHub Actions is configured to test, build and deploy the production package to InfinityFree when `main` changes.


## Production workflow
- Feature branches are used for development.
- Pull requests are merged into `main).
- Every push to `main) runs automated Laravel tests, builds Vite assets and deploys the production package to InfinityFree.
- Secrets remain in GitHub Actions secrets and are never committed.

## Planned platform modules
Services, projects/portfolio, customer leads, digital online services, automation projects, documents/forms, integrations and future AI-assisted workflows can be added without changing the core Laravel architecture.

## Architecture

MRK Laravel Portal is the business-management/backend application for MRK Digital Center. The public WordPress site can handle SEO, content, services and lead acquisition, while this Laravel application manages operational data such as leads, customers, projects, quotations, invoices and blog content.

### Local development

Requirements:
- PHP 8.2+
- Composer
- Node.js 20+
- SQLite or MySQL

Typical setup:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan test
```

For development, use:

```bash
composer run dev
```

### Security

Never commit `.env`, database credentials, FTP credentials, API tokens, OAuth secrets, or other private configuration. Production secrets must be supplied through the hosting environment.

### CI

GitHub Actions runs Composer installation, Laravel migrations, the frontend build, and the automated test suite on pushes and pull requests targeting `main`.
