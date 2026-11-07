# MRK Fitness Google Sheets Backup

This bridge keeps Laravel/MySQL as the primary database. Google Sheets stores a separate backup copy of fitness records. No Firebase or Firebase Functions are used.

## 1. Create the spreadsheet and Apps Script

1. Create a blank Google Sheet in the Google account you want to use for backups.
2. In that sheet, open **Extensions → Apps Script**.
3. Replace the editor contents with `Code.gs` from this folder and save.
4. In Apps Script **Project Settings → Script properties**, add:
   - `SPREADSHEET_ID`: the ID between `/d/` and `/edit` in the Google Sheet URL.
   - `LARAVEL_FEED_URL`: `https://YOUR-LARAVEL-DOMAIN/fitness/backup-feed`
   - `FITNESS_SYNC_TOKEN`: a long random secret (at least 32 characters).
5. Use the same secret in Laravel's environment as `FITNESS_BACKUP_TOKEN`. Do not put the secret in GitHub, source code, screenshots, or public messages.

## 2. Configure Laravel

Set these values in the hosting account's Laravel `.env` file (never commit the real values):

```dotenv
FITNESS_BACKUP_TOKEN=PASTE_THE_SAME_LONG_RANDOM_SECRET_HERE
FITNESS_BACKUP_USER_ID=YOUR_LARAVEL_USER_ID
FITNESS_SHEETS_WEB_APP_URL=
```

After deployment, set `FITNESS_SHEETS_WEB_APP_URL` to the Apps Script Web App URL ending in `/exec`. The user ID must be the account whose fitness records should be backed up. Use the numeric ID from the Laravel `users` table.

The backup feed is a read-only JSON endpoint protected by the Bearer token. It returns only the configured user's fitness records. Keep the token private and rotate it in both systems if it is exposed.

## 3. Deploy Apps Script

1. In Apps Script, choose **Deploy → New deployment → Web app**.
2. Execute as: **Me**.
3. Who has access: **Anyone**. The web app checks the shared secret before accepting a manual sync request.
4. Deploy, authorize the requested Google permissions, and copy the Web App URL ending in `/exec`.
5. Put that URL into Laravel's `FITNESS_SHEETS_WEB_APP_URL` environment variable.

## 4. Enable the daily backup

In Apps Script, select `setupDailyBackup` and click **Run** once. Approve the permissions. It verifies the Laravel feed, creates the first backup, and installs a daily trigger around 2 AM in the Apps Script project timezone. Set the project timezone to **Asia/Karachi** under Project Settings if you want Pakistan time.

The script maintains:
- **Fitness Entries** — latest full copy.
- **Backup History** — timestamp and record count for each successful backup.
- **Daily_YYYYMMDD** — dated daily snapshots.

Daily snapshots contain the latest data available in Laravel when the trigger runs. They do not replace a full database backup.

## 5. Manual sync

Once the Laravel feature is deployed and configured, sign in and use **Sync Google Sheets Backup** on the Fitness Dashboard. This asks Apps Script to fetch a fresh copy immediately.

## Important deployment notes

- The GitHub branch/PR is not the live site. Merge/deploy the application code before configuring the production endpoint.
- Run the new Laravel migration only after exporting a complete SQL backup.
- If the hosting provider blocks outbound requests from PHP, the scheduled Apps Script backups can still work because Apps Script fetches the protected Laravel feed. The Dashboard's manual-sync button may fail in that case; the Apps Script schedule remains independent.
- The feed endpoint requires HTTPS. Never share the token or paste it into a public issue.
