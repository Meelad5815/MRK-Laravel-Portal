# MRK Laravel Portal — GitHub Actions Audit

**Audit snapshot:** 2026-10-16 UTC  
**Repository:** https://github.com/Meelad5815/MRK-Laravel-Portal  
**Scope:** all GitHub Actions run records returned by the GitHub Actions API before this audit-maintenance commit.

## Executive summary

The API reported **541 workflow run records** across the repository from 2026-10-02 through 2026-10-16. All six pages were retrieved (100 + 100 + 100 + 100 + 100 + 41 records).

| Conclusion | Count |
|---|---:|
| Failed | 292 |
| Succeeded | 177 |
| Cancelled | 72 |
| **Total** | **541** |

These are run outcomes, not 292 unique root causes. Repeated CI workflows and repeated monitor checks amplified the number of failed runs.

## Runs by workflow

| Workflow | Runs | Failed | Succeeded | Cancelled |
|---|---:|---:|---:|---:|
| MRK Website Auto Monitor | 118 | 105 | 0 | 13 |
| Laravel CI Check | 136 | 53 | 83 | 0 |
| CI | 24 | 14 | 10 | 0 |
| Deploy Laravel to InfinityFree | 60 | 47 | 13 | 0 |
| MRK Quality Gate | 57 | 36 | 21 | 0 |
| pages build and deployment (GitHub-managed) | 109 | 0 | 50 | 59 |
| .github/workflows/bootstrap-laravel.yml (historical) | 37 | 37 | 0 | 0 |
| **Total** | **541** | **292** | **177** | **72** |

The bootstrap workflow is no longer in the current `.github/workflows` directory; its 37 failures are historical initialization attempts. GitHub Pages runs are platform-generated and should not be confused with Laravel application tests.

## Verified failures and diagnosis

### 1. Production hosting returns no HTTP response

- Deployment run #59: https://github.com/Meelad5815/MRK-Laravel-Portal/actions/runs/38849986788
  - Secrets validation, Composer setup, frontend build, application tests, production dependency installation, packaging and FTP sync passed.
  - The migration request failed with `curl: (52) Empty reply from server`.
- Website monitor run #117: https://github.com/Meelad5815/MRK-Laravel-Portal/actions/runs/38850193706
  - Six root/direct-public static, PHP diagnostic and Laravel probes returned HTTP 000, zero bytes and `curl: (52) Empty reply from server`.
- Deployment run #60: https://github.com/Meelad5815/MRK-Laravel-Portal/actions/runs/38850648915
  - The new preflight failed before FTP upload with the same curl error; FTP and migrations were correctly skipped.
- Website monitor run #118: https://github.com/Meelad5815/MRK-Laravel-Portal/actions/runs/38850712847
  - The hosting-layer probe failed again.

**Conclusion:** this is not currently evidenced as a Laravel-only problem. The host is not returning HTTP responses even for static content. The GitHub workflow can detect and prevent risky uploads, but it cannot repair InfinityFree's external web-server/domain service.

### 2. Historical test assertion mismatch

Several CI runs around 2026-10-15 failed at `Tests\\Feature\\BusinessPlatformTest > invoice paid amount is capped at total`. The failure was a strict string comparison between `10000.00` and `10000`, not a demonstrated invoice calculation defect. The current test uses a numeric assertion and the current CI Check run #136 passed:
https://github.com/Meelad5815/MRK-Laravel-Portal/actions/runs/38850649025

### 3. Historical secret validation

Deployment run #58 failed the required-secret validation. Run #59 passed that step, so the current recorded failure is no longer missing-secret validation. Secret values were not accessed or modified during this audit.

### 4. Redundant automation

Before this audit, `ci.yml`, `ci-check.yml`, and `mrk-quality-gate.yml` all ran on pushes/pull requests, producing overlapping tests and repeated failure counts. The maintenance commit changes `ci-check.yml` to be the single push/PR validation workflow, retains the quality gate for scheduled/manual audits, and leaves the legacy CI workflow available for manual runs only.

## Changes in this audit-maintenance commit

1. Kept the deployment HTTP preflight introduced in commit `2fafacd98c73f1cf510a2819a3d4408654f00d7c`. It stops before FTP if the public static probe is unavailable.
2. Disabled duplicate push/PR triggers in the legacy `ci.yml` and extended `mrk-quality-gate.yml`.
3. Added remote IP to website-monitor probe output to help correlate future failures with hosting-provider incidents.
4. Added this report for traceability.

## Remaining external dependency

The current public InfinityFree status page reports no active general incident, but that does not rule out an account/server-specific fault: https://status.infinityfree.com/

No claim is made that the production website is fixed. A successful end-to-end repair requires the hosting endpoint to return HTTP responses, after which deployment and Laravel/database health can be verified.
