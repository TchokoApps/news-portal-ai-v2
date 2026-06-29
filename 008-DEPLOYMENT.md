# 008 - Deployment Guide

⚡ **IMPORTANT:** Always use the caveman skill for concise communication.

**Date:** June 18, 2026  
**Status:** ✅ Ready  
**Project:** News Portal AI v2

---

## Table of Contents

1. [Pre-Deployment Checklist](#pre-deployment-checklist)
2. [Deployment Platforms](#deployment-platforms)
3. [Server Setup](#server-setup)
4. [Environment Configuration](#environment-configuration)
5. [Database Migration](#database-migration)
6. [Optimization](#optimization)

---

## Pre-Deployment Checklist

### Code Quality

- [ ] Run tests: `php artisan test`
- [ ] Fix style issues: `./vendor/bin/pint`
- [ ] No debug functions (dd, dump) left in code
- [ ] All console errors resolved
- [ ] No database errors in logs

### Configuration

- [ ] `.env` file properly configured
- [ ] `APP_DEBUG` set to `false`
- [ ] `APP_ENV` set to `production`
- [ ] All required environment variables set
- [ ] Secrets not committed to version control

### Database

- [ ] Migrations tested locally
- [ ] Data backup created
- [ ] Foreign keys properly defined
- [ ] Indexes created for performance

### Security

- [ ] HTTPS enabled
- [ ] Security headers configured
- [ ] CSRF protection enabled (automatic in Blade)
- [ ] Passwords properly hashed
- [ ] API rate limiting configured

### Testing

- [ ] Authentication tested (user & admin)
- [ ] User registration flow tested
- [ ] Admin dashboard accessible
- [ ] Routes accessible
- [ ] Database connectivity verified

### Assets

- [ ] Frontend assets built: `npm run build`
- [ ] Assets minified and versioned
- [ ] No 404 errors for static files
- [ ] CDN configured (if using)

### Documentation

- [ ] README updated
- [ ] Installation instructions clear
- [ ] Deployment steps documented
- [ ] Configuration documented

---

## Deployment Platforms

### Shared Hosting (cPanel/Plesk)

**Requirements:**
- PHP 8.2+
- Composer access
- Command line access
- MySQL/PostgreSQL

**Steps:**

1. **Upload Files**
   ```bash
   git clone <repo> or upload via SFTP
   ```

2. **Install Dependencies**
   ```bash
   composer install --no-dev -o
   npm install
   npm run build
   ```

3. **Configure Environment**
   ```bash
   cp .env.example .env
   # Edit .env with production settings
   php artisan key:generate
   ```

4. **Setup Database**
   ```bash
   php artisan migrate --force
   php artisan migrate:seed
   ```

5. **Set Permissions**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chmod -R 777 storage bootstrap/cache
   ```

6. **Point Web Root**
   - Set public directory to `public/`
   - Update DNS records

### DigitalOcean App Platform

**Requirements:**
- DigitalOcean account
- GitHub repository
- Credit card

**Steps:**

1. **Create App**
   - Select PHP component
   - Connect GitHub repo
   - Auto-deploys on push

2. **Configure Environment**
   - Set environment variables
   - Add database

3. **Deploy**
   - Push to main branch
   - Automatic deployment

**Environment Variables (in App Platform):**
```
APP_NAME=NewsPortal
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_CONNECTION=pgsql
DB_HOST=<db-host>
DB_DATABASE=news_portal
DB_USERNAME=<db-user>
DB_PASSWORD=<secure-password>
```

### Heroku (Deprecated - Use Alternatives)

**Alternative: Railway, Render, Fly.io**

### Docker Deployment

**Docker Setup:**

```dockerfile
# Dockerfile
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    composer \
    npm \
    git \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev -o

COPY package.json package-lock.json ./
RUN npm install && npm run build

COPY . .

RUN chmod -R 755 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
```

**Docker Compose:**

```yaml
# docker-compose.yml
version: '3.8'

services:
  app:
    build: .
    volumes:
      - ./:/app
    depends_on:
      - db
    environment:
      DB_HOST: db
      DB_DATABASE: news_portal
      DB_USERNAME: root
      DB_PASSWORD: password

  nginx:
    image: nginx:latest
    ports:
      - "80:80"
    volumes:
      - ./public:/app/public
      - ./nginx.conf:/etc/nginx/nginx.conf
    depends_on:
      - app

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: password
      MYSQL_DATABASE: news_portal
    volumes:
      - db-data:/var/lib/mysql

volumes:
  db-data:
```

---

## Server Setup

### Linux Server (Ubuntu 22.04 LTS)

**1. Update System**
```bash
sudo apt update && sudo apt upgrade -y
```

**2. Install PHP Stack**
```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php
sudo apt update

# Install PHP and extensions
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-pgsql php8.2-curl \
    php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip

# Install Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js and npm
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install nginx
sudo apt install -y nginx

# Install MySQL
sudo apt install -y mysql-server
```

**3. Create Database**
```bash
sudo mysql -u root
CREATE DATABASE news_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'news_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON news_portal.* TO 'news_user'@'localhost';
FLUSH PRIVILEGES;
exit;
```

**4. Clone Application**
```bash
cd /var/www
git clone <repo> news-portal-ai-v2
cd news-portal-ai-v2
```

**5. Install Dependencies**
```bash
composer install --no-dev -o
npm install && npm run build
```

**6. Configure Laravel**
```bash
cp .env.example .env
php artisan key:generate

# Edit .env with production settings
sudo nano .env
```

**7. Setup Permissions**
```bash
sudo chown -R www-data:www-data .
sudo chmod -R 755 storage bootstrap/cache
sudo chmod -R 777 storage bootstrap/cache
```

**8. Configure Nginx**
```nginx
# /etc/nginx/sites-available/news-portal
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/news-portal-ai-v2/public;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**9. Enable Site and Restart**
```bash
sudo ln -s /etc/nginx/sites-available/news-portal /etc/nginx/sites-enabled/
sudo systemctl restart nginx php8.2-fpm
```

**10. SSL Certificate (Let's Encrypt)**
```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Get certificate
sudo certbot certonly --nginx -d yourdomain.com -d www.yourdomain.com

# Update nginx config with SSL paths
# Certbot can auto-configure
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renew
sudo systemctl enable certbot.timer
```

**11. Run Migrations**
```bash
php artisan migrate --force
```

---

## Environment Configuration

### Production .env File

```bash
APP_NAME="News Portal"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=news_portal
DB_USERNAME=news_user
DB_PASSWORD=secure_password_here

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@newsportal.com
MAIL_FROM_NAME="News Portal"
```

### Security Environment Variables

```bash
# Generate 32-character random string
RANDOM_STRING=$(tr -dc A-Za-z0-9 </dev/urandom | head -c 32)
```

---

## Database Migration

### From Development to Production

```bash
# On development machine
# Backup local database
sqlite3 database/database.sqlite ".dump" > backup.sql

# Generate migration script
php artisan migrate:status > migration_status.txt

# On production server
# Create empty production database
php artisan migrate --force

# Verify migrations
php artisan migrate:status
```

### Data Migration

```php
// Create migration for initial data
php artisan make:migration seed_initial_admins

// In migration file
public function up(): void
{
    Admin::create([
        'name' => 'Super Admin',
        'email' => 'admin@newsportal.com',
        'password' => bcrypt('change-me-immediately'),
        'role' => 'super_admin',
    ]);
}
```

---

## Optimization

### Production Optimization

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache events
php artisan event:cache

# Optimize auto-loading
composer install --no-dev -o

# Clear unnecessary files
rm -rf node_modules
```

### Performance Tuning

**PHP Configuration (`php.ini`):**
```ini
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 50M
post_max_size = 50M

; Production settings
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log
```

**Nginx Configuration:**
```nginx
# Add caching headers
location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2)$ {
    expires 30d;
    add_header Cache-Control "public, immutable";
}

# Gzip compression
gzip on;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
gzip_min_length 1000;
```

### File Permissions ✅

**Directory Permissions for Profile Upload:**

```bash
# Create upload directory
mkdir -p public/uploads/profiles

# Set proper permissions
chmod 755 public/uploads
chmod 755 public/uploads/profiles

# Set web server user ownership (Ubuntu/Linux)
sudo chown -R www-data:www-data public/uploads
sudo chown -R www-data:www-data storage

# Allow Laravel to write to directories
chmod 775 storage
chmod 775 bootstrap/cache
chmod 775 public/uploads/profiles
```

**Production Checklist:**
- ✅ `storage/` writable by web server
- ✅ `bootstrap/cache/` writable by web server
- ✅ `public/uploads/profiles/` writable by web server (for profile images)
- ✅ `.env` file readable only by web server (not world-readable)
- ✅ `config/` directory not world-writable

### Database Optimization

```php
// Add indexes
php artisan make:migration add_indexes

// In migration
Schema::table('articles', function (Blueprint $table) {
    $table->index(['status', 'published_at']);
    $table->index('author_id');
});
```

### Monitoring

**Log Monitoring:**
```bash
# Real-time log view
tail -f storage/logs/laravel.log

# Error analysis
grep "error\|exception\|fatal" storage/logs/laravel.log | tail -20
```

**Health Check:**
```bash
# Laravel built-in health check
curl https://yourdomain.com/up
```

---

## Post-Deployment

### Verification

```bash
# Check routes
php artisan route:list

# Test database connection
php artisan migrate:status

# View logs
php artisan tail

# Test email
php artisan tinker
>>> Mail::raw('Test', function($m) { $m->to('test@example.com'); });
```

### Backups

**Automated Backups:**
```bash
# Cron job (edit with crontab -e)
# Daily database backup at 2 AM
0 2 * * * mysqldump -u root -p news_portal | gzip > /backup/db_$(date +\%Y\%m\%d).sql.gz

# Weekly file backup
0 3 * * 0 tar -czf /backup/files_$(date +\%Y\%m\%d).tar.gz /var/www/news-portal-ai-v2
```

### Monitoring

**Uptime Monitoring:**
- Use services like UptimeRobot, Pingdom, or StatusPage

**Error Tracking:**
- Integrate Sentry, Bugsnag, or Rollbar

**Performance Monitoring:**
- Use Blackfire, New Relic, or DataDog

---

## Rollback Procedure

### If Deployment Fails

```bash
# Check recent commits
git log --oneline -10

# Rollback to previous version
git revert <commit-hash>
git push origin main

# Re-deploy
# (Platform auto-deploys or run deployment script)

# Database rollback (if migrations failed)
php artisan migrate:rollback
```

---

## Next Steps

1. **Troubleshooting:** [009-TROUBLESHOOTING.md](./009-TROUBLESHOOTING.md)
2. **Monitoring:** Set up error tracking and monitoring
3. **Backup:** Establish backup procedures
4. **Documentation:** Maintain deployment runbook
