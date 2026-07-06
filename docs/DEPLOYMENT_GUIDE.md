# Deployment Guide - Bank Sampah Faperta

Complete guide for deploying the application to production environments.

---

## 📋 Pre-Deployment Checklist

- [ ] Production server meets minimum requirements
- [ ] Database server configured and accessible
- [ ] Domain name and DNS configured
- [ ] SSL certificate obtained (Let's Encrypt recommended)
- [ ] Environment variables prepared
- [ ] Backup strategy implemented
- [ ] Monitoring tools configured

---

## 🖥️ Server Requirements

### Minimum Specifications

- **OS:** Ubuntu 22.04 LTS or similar
- **CPU:** 2 cores
- **RAM:** 4GB
- **Storage:** 20GB SSD
- **PHP:** 8.3 or higher
- **Database:** MySQL 8.0+ / PostgreSQL 13+
- **Web Server:** Nginx or Apache with mod_rewrite
- **Node.js:** 18+ (for building assets)

### Required PHP Extensions

```bash
php8.3-cli php8.3-fpm php8.3-mysql php8.3-pgsql php8.3-sqlite3
php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath
php8.3-gd php8.3-intl php8.3-redis
```

---

## 🚀 Deployment Options

### Option 1: Traditional VPS Deployment

Recommended for full control and cost optimization.

### Option 2: Platform as a Service (PaaS)

- **Laravel Forge** - Automated deployment and server management
- **Laravel Vapor** - Serverless deployment on AWS Lambda
- **DigitalOcean App Platform** - Managed platform
- **Heroku** - Quick deployment (with limitations)

### Option 3: Containerized Deployment

Using Docker and Docker Compose for consistent environments.

---

## 📦 Option 1: VPS Deployment (Ubuntu 22.04)

### Step 1: Server Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server git curl supervisor

# Install PHP 8.3
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring \
  php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath php8.3-gd php8.3-intl

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### Step 2: Database Setup

**MySQL:**
```bash
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

```sql
CREATE DATABASE banksampah_faperta CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'banksampah'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON banksampah_faperta.* TO 'banksampah'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Deploy Application

```bash
# Create directory
sudo mkdir -p /var/www/banksampah-faperta
sudo chown $USER:$USER /var/www/banksampah-faperta

# Clone repository
cd /var/www/banksampah-faperta
git clone https://github.com/jey41/banksampah-faperta.git .

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install --production

# Setup environment
cp .env.example .env
nano .env  # Edit with production values
php artisan key:generate

# Build assets
npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/banksampah-faperta
sudo chmod -R 755 /var/www/banksampah-faperta
sudo chmod -R 775 /var/www/banksampah-faperta/storage
sudo chmod -R 775 /var/www/banksampah-faperta/bootstrap/cache

# Create storage link
php artisan storage:link

# Run migrations
php artisan migrate --force
```

### Step 4: Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/banksampah-faperta
```

```nginx
server {
    listen 80;
    server_name banksampah-faperta.com www.banksampah-faperta.com;
    root /var/www/banksampah-faperta/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/banksampah-faperta /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Step 5: SSL Certificate (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d banksampah-faperta.com -d www.banksampah-faperta.com
```

### Step 6: Queue Worker Setup

```bash
sudo nano /etc/supervisor/conf.d/banksampah-worker.conf
```

```ini
[program:banksampah-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/banksampah-faperta/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/banksampah-faperta/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start banksampah-worker:*
```

### Step 7: Scheduler (Cron)

```bash
sudo crontab -e -u www-data
```

Add:
```
* * * * * cd /var/www/banksampah-faperta && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🐳 Option 2: Docker Deployment

### Dockerfile

Create `Dockerfile` in project root:

```dockerfile
FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

EXPOSE 9000
CMD ["php-fpm"]
```

### docker-compose.yml

```yaml
version: '3.8'

services:
  app:
    build: .
    container_name: banksampah-app
    volumes:
      - ./:/var/www
      - ./storage:/var/www/storage
    networks:
      - banksampah-network

  nginx:
    image: nginx:alpine
    container_name: banksampah-nginx
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx/conf.d:/etc/nginx/conf.d
    networks:
      - banksampah-network

  mysql:
    image: mysql:8.0
    container_name: banksampah-mysql
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: banksampah_faperta
      MYSQL_USER: banksampah
      MYSQL_PASSWORD: secure_password
    volumes:
      - mysql-data:/var/lib/mysql
    networks:
      - banksampah-network

networks:
  banksampah-network:
    driver: bridge

volumes:
  mysql-data:
```

### Deploy with Docker

```bash
docker-compose up -d
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan storage:link
```

---

## 🔧 Production Environment Configuration

### Critical .env Settings

```env
APP_NAME="Bank Sampah Faperta"
APP_ENV=production
APP_DEBUG=false  # MUST be false in production
APP_URL=https://banksampah-faperta.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=banksampah_faperta
DB_USERNAME=banksampah
DB_PASSWORD=secure_password_here

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@banksampah-faperta.com"
MAIL_FROM_NAME="${APP_NAME}"

FILESYSTEM_DISK=public

# Production optimizations
CACHE_PREFIX=bsfp_
```

### Optimization Commands

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

---

## 📊 Monitoring & Logging

### Application Monitoring

**Laravel Telescope (Development/Staging only):**
```bash
composer require laravel/telescope --dev
php artisan telescope:install
```

**Production Monitoring:**
- **Sentry** - Error tracking and performance monitoring
- **Laravel Pulse** - Application health monitoring
- **New Relic** - APM solution

### Log Management

```bash
# View logs
tail -f storage/logs/laravel.log

# Log rotation (add to /etc/logrotate.d/banksampah)
/var/www/banksampah-faperta/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 www-data www-data
}
```

---

## 🔄 Zero-Downtime Deployment Strategy

### Using Deployer or Envoy

**Install Deployer:**
```bash
composer require deployer/deployer --dev
```

**deploy.php:**
```php
<?php
namespace Deployer;

require 'recipe/laravel.php';

set('application', 'Bank Sampah Faperta');
set('repository', 'git@github.com:jey41/banksampah-faperta.git');
set('keep_releases', 5);

host('production')
    ->set('remote_user', 'deploy')
    ->set('deploy_path', '/var/www/banksampah-faperta');

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'artisan:storage:link',
    'artisan:migrate',
    'artisan:cache:clear',
    'artisan:config:cache',
    'artisan:route:cache',
    'artisan:view:cache',
    'deploy:publish',
]);

after('deploy:failed', 'deploy:unlock');
```

**Deploy:**
```bash
./vendor/bin/dep deploy production
```

---

## 🔐 Security Hardening

### Server Security

```bash
# Configure firewall
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable

# Disable PHP exposure
sudo nano /etc/php/8.3/fpm/php.ini
# Set: expose_php = Off

# Limit PHP execution
# Set: max_execution_time = 30
# Set: memory_limit = 256M
```

### Application Security

- Keep `APP_DEBUG=false` in production
- Use strong `APP_KEY` (never reuse across environments)
- Implement rate limiting on routes
- Enable CSRF protection (Laravel default)
- Use HTTPS only (enforce in config)
- Keep dependencies updated regularly

---

## 💾 Backup Strategy

### Database Backups

**Automated MySQL backup script:**
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/mysql"
DB_NAME="banksampah_faperta"
DB_USER="banksampah"
DB_PASS="password"

mkdir -p $BACKUP_DIR
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/$DB_NAME-$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -type f -mtime +30 -delete
```

**Schedule backup (crontab):**
```bash
0 2 * * * /path/to/backup-script.sh
```

### Application Files Backup

```bash
# Backup storage directory
tar -czf storage-backup-$(date +%Y%m%d).tar.gz storage/app
```

---

## 🔄 Update & Maintenance

### Updating the Application

```bash
cd /var/www/banksampah-faperta

# Pull latest code
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader
npm install --production
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo supervisorctl restart banksampah-worker:*
sudo systemctl reload php8.3-fpm
```

---

## 📞 Troubleshooting

### Permission Issues

```bash
sudo chown -R www-data:www-data /var/www/banksampah-faperta
sudo chmod -R 755 /var/www/banksampah-faperta
sudo chmod -R 775 /var/www/banksampah-faperta/storage
sudo chmod -R 775 /var/www/banksampah-faperta/bootstrap/cache
```

### 500 Internal Server Error

- Check `storage/logs/laravel.log`
- Ensure `APP_DEBUG=true` temporarily to see error details
- Verify file permissions
- Check PHP error logs: `/var/log/php8.3-fpm.log`

### Queue Not Processing

```bash
sudo supervisorctl status
sudo supervisorctl restart banksampah-worker:*
```

---

*For additional support, refer to [Laravel Deployment Documentation](https://laravel.com/docs/11.x/deployment).*
