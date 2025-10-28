# Database Migration Safety Guidelines

## ⚠️ IMPORTANT: Never Run These Commands on Production

**NEVER run these destructive commands without a backup:**

-   `php artisan migrate:fresh`
-   `php artisan migrate:fresh --seed`
-   `php artisan db:wipe`

These commands **DROP ALL TABLES** and will result in complete data loss!

## ✅ Safe Migration Commands

### Running New Migrations

```bash
# Always use regular migrate (not fresh)
php artisan migrate

# On production, always use --force flag
php artisan migrate --force

# Check migration status first
php artisan migrate:status
```

### Before Running Migrations

1. **Create a database backup:**

```bash
# Create backups directory
New-Item -ItemType Directory -Force -Path .\storage\backups

# Backup database (replace credentials)
mysqldump -u [user] -p [database_name] > .\storage\backups\backup_$(Get-Date -Format 'yyyy-MM-dd_HHmmss').sql
```

2. **Test with --pretend flag:**

```bash
php artisan migrate --pretend
```

3. **Verify migration files** don't have destructive operations

## 🔄 Rolling Back Migrations

```bash
# Rollback last batch
php artisan migrate:rollback

# Rollback specific steps
php artisan migrate:rollback --step=2

# Check status after rollback
php artisan migrate:status
```

## 🆘 Recovery from Accidental migrate:fresh

If you accidentally ran `migrate:fresh`:

1. **Stop immediately** - Don't run any more commands
2. **Restore from backup:**

```bash
mysql -u [user] -p [database_name] < .\storage\backups\[backup_file].sql
```

3. **Verify restoration:**

```bash
php artisan migrate:status
```

4. **Mark existing migrations as ran** if tables exist but migrations table shows pending

## 📝 Best Practices

### Development

-   Use `migrate:fresh --seed` freely in local development
-   Keep seeders up to date with sample data
-   Create backups before major migration changes

### Staging

-   Always backup before migrating
-   Test migrations on staging before production
-   Use `migrate --pretend` to review changes
-   Document any manual data migrations needed

### Production

-   **ALWAYS backup first** (no exceptions!)
-   Use `migrate --force` (never fresh)
-   Run during maintenance windows
-   Have rollback plan ready
-   Monitor application after migration
-   Keep database backups for at least 30 days

## 🔐 Automated Backup

Consider setting up automated daily backups:

### Windows Task Scheduler (Laragon)

```powershell
# Create a backup script: backup_db.ps1
$timestamp = Get-Date -Format "yyyy-MM-dd_HHmmss"
$backupDir = "C:\laragon\www\ballie\storage\backups"
$dbName = "ballie_db"
$dbUser = "root"
$dbPass = ""

if (!(Test-Path $backupDir)) {
    New-Item -ItemType Directory -Force -Path $backupDir
}

# Keep only last 7 days of backups
Get-ChildItem $backupDir -Filter "*.sql" |
    Where-Object {$_.LastWriteTime -lt (Get-Date).AddDays(-7)} |
    Remove-Item

# Create new backup
& "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe" -u $dbUser $dbName > "$backupDir\backup_$timestamp.sql"
```

Schedule this script to run daily via Task Scheduler.

## 🚨 Emergency Contacts

**Before running destructive operations:**

-   Ensure you have recent backups
-   Test on staging first
-   Have database credentials ready
-   Know how to restore from backup
-   Document the reason for destructive operation

## 📊 Current State After Recovery (October 27, 2025)

-   ✅ Database restored from online server backup
-   ✅ All 92 migrations now ran successfully
-   ✅ Banks table created
-   ✅ Bank reconciliations tables created
-   ✅ All features operational

**Last Incident:**

-   Date: October 27, 2025
-   Issue: Accidental `migrate:fresh` during bank reconciliation feature development
-   Resolution: Restored from online server database dump
-   Tables affected: All (~72 tables dropped)
-   Recovery method: Import backup + mark existing migrations + run new migrations
-   Data loss: None (backup was current)
-   Lesson: Always backup before running migrations, never use fresh on dataful databases

---

**Remember:** A 5-minute backup can save 5 hours of recovery work!
