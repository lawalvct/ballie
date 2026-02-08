# ✅ Fixed Composer + PHP Extensions on Ubuntu 24.04 (aaPanel)

**Problem:**
Composer failed because required PHP extensions were missing (`fileinfo`, `dom`, `gd`, `xml`, `curl`, etc.).

---

## ✅ Solution

### 1) Install required PHP 8.3 extensions

```bash
sudo apt update
sudo apt install -y php8.3-dom php8.3-xml php8.3-gd php8.3-curl php8.3-mbstring php8.3-mysql php8.3-bcmath php8.3-intl
```

### 2) Mark the repo as safe (git ownership warning)

```bash
git config --global --add safe.directory /www/wwwroot/ballie.co
```

### 3) Verify extensions are enabled

```bash
php -m | grep -iE "dom|gd|xml|curl|mbstring|mysql|fileinfo"
```

### 4) Run Composer

```bash
php /usr/local/bin/composer install
```

---

✅ After this, Composer installs successfully.

# Go to your project root

cd /www/wwwroot/ballie.co

# Make sure the web server user owns the files (aaPanel usually uses 'www')

chown -R www:www /www/wwwroot/ballie.co

# Give read/write/execute permissions to storage and cache

chmod -R 775 storage bootstrap/cache

# If you want to be extra safe, also set permissions for the whole project

find /www/wwwroot/ballie.co -type f -exec chmod 644 {} \;
find /www/wwwroot/ballie.co -type d -exec chmod 755 {} \;
npm install
or

apt install npm

if other pages are not showing
location / {
try_files $uri $uri/ /index.php?$query_string;
}

    in .config
