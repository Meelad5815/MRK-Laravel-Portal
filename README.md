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
