# Database Migration Scripts

This directory contains SQL migration scripts for the Biggest Talent Africa application.

## Purpose
These scripts are designed to be run on the production database to add new features or update the database schema.

## ⚡ Quick Start (Recommended)

### Run via PHP Script (Easiest Method)
Simply navigate to this URL in your browser:
```
http://localhost/SustecAfrica/btagrace/dbaddons/run_migrations.php
```

Or for production:
```
https://yourdomain.com/dbaddons/run_migrations.php
```

The script will:
- ✅ Show you what migrations will run
- ✅ Execute all migrations in the correct order
- ✅ Display real-time progress and results
- ✅ Provide detailed error messages if anything fails
- ✅ Show a summary of successful and failed migrations

**That's it! No command line needed.**

## Migration Scripts

### 001_create_countries_table.sql
Creates the `countries` table to store all world countries with ISO codes.

**Run this first.**

### 002_create_states_provinces_table.sql
Creates the `states_provinces` table to store states/provinces for all countries.

**Run this after 001.**

### 003_populate_countries.sql
Populates the `countries` table with 195+ countries worldwide.

**Run this after 002.**

### 004_populate_states_provinces.sql
Populates the `states_provinces` table with states/provinces for major countries including:
- Zambia (10 provinces)
- United States (50 states)
- Canada (10 provinces)
- United Kingdom (4 countries)
- Australia (6 states)
- South Africa (9 provinces)
- Nigeria (37 states)
- Kenya (5 major counties)
- India (8 major states)

**Run this after 003.**

## How to Run on Production

### Method 1: Using phpMyAdmin
1. Log into phpMyAdmin on your production server
2. Select your database
3. Click on "SQL" tab
4. Copy and paste the contents of each script in order (001 → 002 → 003 → 004)
5. Click "Go" to execute each script

### Method 2: Using MySQL Command Line
```bash
mysql -u your_username -p your_database < 001_create_countries_table.sql
mysql -u your_username -p your_database < 002_create_states_provinces_table.sql
mysql -u your_username -p your_database < 003_populate_countries.sql
mysql -u your_username -p your_database < 004_populate_states_provinces.sql
```

### Method 3: Using a Single Command
```bash
cd dbaddons
for file in *.sql; do mysql -u your_username -p your_database < $file; done
```

## Important Notes

- **Backup First**: Always backup your database before running migration scripts
- **Run in Order**: Scripts must be run in numerical order (001, 002, 003, 004)
- **Idempotent**: Scripts use `IF NOT EXISTS` where applicable, so they can be run multiple times safely
- **No Data Loss**: These scripts only ADD new tables and data. They do not modify or delete existing data.

## Verification

After running all scripts, verify the installation:

```sql
-- Check countries table
SELECT COUNT(*) FROM countries;
-- Should return 195+

-- Check states_provinces table
SELECT COUNT(*) FROM states_provinces;
-- Should return 200+

-- Check Zambia provinces
SELECT sp.name 
FROM states_provinces sp
JOIN countries c ON sp.country_id = c.id
WHERE c.iso_code = 'ZM';
-- Should return 10 provinces
```

## Rollback

If you need to rollback these changes:

```sql
DROP TABLE IF EXISTS states_provinces;
DROP TABLE IF EXISTS countries;
```

**Warning**: This will delete all country and state/province data.
