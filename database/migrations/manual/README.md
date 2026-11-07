# InfinityFree manual database migration — MRK Fitness

InfinityFree free hosting can reject automated migration requests even when GitHub Actions successfully uploads the application files. This SQL is the safe, additive alternative for the single Fitness Dashboard table.

## Before running

1. Export a fresh SQL backup of the Laravel database from phpMyAdmin.
2. In phpMyAdmin, select the Laravel database (the one used by MRK-Laravel-Portal, not the separate Drupal database).
3. Confirm the selected database name in the left sidebar before opening the SQL tab.
4. Do not run this against any other database.

## Run

Open [fitness_entries.sql](fitness_entries.sql), copy its complete contents, paste into phpMyAdmin's SQL tab, and click Go once.

The script:
- Creates only the `fitness_entries` table if it does not already exist.
- Adds a foreign key to the existing `users.id` column.
- Adds the matching Laravel migration record only if it is not already present.
- Does not drop, truncate, or modify existing application tables.

## Verify

Run these queries in phpMyAdmin:

```sql
SHOW TABLES LIKE 'fitness_entries';

SELECT migration, batch
FROM migrations
WHERE migration = '2026_11_05_000001_create_fitness_entries_table';
```

Both queries should return one row/result. If phpMyAdmin reports an error, stop and share the exact error text; do not repeatedly rerun a partially failed script without checking table and migration state.

## Important

The GitHub deployment workflow uploads the application package over FTP. It deliberately does not expose a public one-time migration endpoint because InfinityFree returned HTTP 404 during the protected browser migration attempt. The database step remains manual until the hosting provider offers a reliable CLI/SSH migration route.
