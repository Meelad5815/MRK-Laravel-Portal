# InfinityFree manual database migration safety checklist

## Current repository audit (2026-10-17)

The repository has Laravel migrations for users, cache, jobs, leads, the admin flag, projects, customers, services, customer/project links, quotes, invoices, site settings, and blog posts.

**Do not import `database/infinityfree.sql` blindly into an existing database.** The file currently creates only a subset of the application tables and inserts migration-history rows for only a subset of migrations. It is not a complete upgrade script for an existing database. Some migration filenames also share the same timestamp prefix; Laravel tracks the full migration name, but the schema state still needs to be checked before applying changes.

## Step 1 — Back up first

In InfinityFree control panel, open phpMyAdmin, select the application's database, choose **Export**, choose **Quick** and **SQL**, then download the backup. Do not proceed without a backup.

## Step 2 — Inspect existing tables (read-only)

Select the application's database in phpMyAdmin and run:

```sql
SHOW TABLES;
```

Then inspect these tables individually if present:

```sql
SHOW CREATE TABLE users;
SHOW CREATE TABLE leads;
SHOW CREATE TABLE projects;
SHOW CREATE TABLE customers;
SHOW CREATE TABLE services;
SHOW CREATE TABLE quotes;
SHOW CREATE TABLE invoices;
SHOW CREATE TABLE site_settings;
SHOW CREATE TABLE blog_posts;
SHOW CREATE TABLE migrations;
```

If any table does not exist, its `SHOW CREATE TABLE` query will report an error; that is an inspection result, not a reason to import the full schema.

## Step 3 — Check Laravel migration history

```sql
SELECT migration, batch
FROM migrations
ORDER BY batch, migration;
```

Compare the result with the files in `database/migrations/`. A table existing does not necessarily mean Laravel has recorded its migration; and a migration recorded as completed does not prove that every expected column exists.

## Step 4 — Do not guess an upgrade SQL script

Before running any `ALTER TABLE`, confirm actual table definitions and migration history. The safe SQL differs depending on which tables and columns already exist. In particular, the customer foreign keys on `leads`, `projects`, `quotes`, and `invoices` require the referenced `customers` table to exist with a compatible primary key.

Once the inspection output is available, prepare a targeted, additive SQL script for only the missing tables/columns. Avoid `DROP TABLE`, destructive column changes, and importing sample/admin credentials.

## Hosting limitation

The InfinityFree free-plan browser challenge blocks automated GitHub Actions requests to the site. The CI workflow can validate repository code, but it cannot prove a production database migration completed. Do not expose deployment keys, database passwords, or the contents of `.env` in issues or chat.
