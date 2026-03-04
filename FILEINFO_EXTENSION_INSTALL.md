# Installing PHP fileinfo Extension on aaPanel (PHP 8.3, Ubuntu)

## Problem
aaPanel PHP 8.3 was compiled with `--disable-fileinfo`, so the `fileinfo` extension is not available.

Confirmed via:
```bash
/www/server/php/83/bin/php -i | grep -i "Configure Command"
# Output shows: '--disable-fileinfo'
```

---

## Root Cause of Compilation Failure

When trying to compile `fileinfo` via `make`, it was killed by the OS:

```
cc: fatal error: Killed signal terminated program cc1
compilation terminated.
make: *** [Makefile:220: libmagic/apprentice.lo] Error 1
```

**Reason:** Server ran out of RAM. The Linux OOM (Out-of-Memory) killer terminated the compiler process.

---

## Solution: Add Temporary Swap, Then Compile

### Step 1: Create 2GB Swap Space

```bash
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
free -m
```

### Step 2: Clean and Recompile fileinfo

```bash
cd /www/server/php/83/src/ext/fileinfo
make clean
make -j1
make install
```

> Use `-j1` (single-threaded) to reduce peak memory usage.

### Step 3: Enable the Extension

Add to `php.ini`:

```bash
echo "extension=fileinfo.so" >> /www/server/php/83/etc/php.ini
```

Or if aaPanel scans a `php.d` directory:

```bash
sudo mkdir -p /www/server/php/83/etc/php.d
echo "extension=fileinfo.so" | sudo tee /www/server/php/83/etc/php.d/20-fileinfo.ini
```

### Step 4: Restart PHP-FPM

```bash
/etc/init.d/php-fpm-83 restart
```

### Step 5: Verify

```bash
/www/server/php/83/bin/php -m | grep -i fileinfo
/www/server/php/83/bin/php --ri fileinfo
```

You should see `fileinfo` listed.

### Step 6: Remove Swap (After Success)

```bash
sudo swapoff /swapfile
sudo rm /swapfile
```

---

## Alternative: Rebuild PHP 8.3 via aaPanel UI

1. Go to **aaPanel → App Store → PHP 8.3 → Settings → Compile Options**
2. Remove `--disable-fileinfo` from the compile flags
3. Click **Rebuild / Compile PHP 8.3**
4. Restart PHP-FPM and Nginx from aaPanel after rebuild

---

## RAM Requirements

| Option | Minimum Free RAM Needed |
|--------|------------------------|
| Compile fileinfo extension | ~1GB free |
| Recommended for stability | 2GB free |

---

## Notes

- `sudo apt install php8.3-common` and `sudo phpenmod fileinfo` only affect **system PHP** (`/usr/bin/php`), NOT aaPanel PHP located at `/www/server/php/83/`.
- Always use `/www/server/php/83/bin/php` to manage aaPanel's PHP 8.3.
- Swap is a free, temporary solution that is fully safe to add and remove.
