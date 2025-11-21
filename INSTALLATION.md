# UniPost Installation Guide

## Overview
UniPost is a Laravel 11-based social media management platform with Vue 3 frontend.

## System Requirements
- PHP 8.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer 2.x
- Node.js 18+ and NPM
- Web server (Apache/Nginx)

## Installation Methods

### Method 1: Using Installation Wizard (Recommended for aaPanel)

1. Upload all files to your web server
2. Point your domain/subdomain to the `/public` directory
3. Visit your domain in a browser (e.g., https://yourdomain.com)
4. Follow the installation wizard:
   - Step 1: System requirements check
   - Step 2: Database configuration
   - Step 3: Application settings
   - Step 4: Social media API keys (optional)
   - Step 5: Installation
5. Delete `install.php` after successful installation

### Method 2: Manual Installation

#### Step 1: Upload Files
Upload all files to your server (e.g., `/www/wwwroot/yourdomain.com/`)

#### Step 2: Configure Environment
```bash
cp .env.example .env
```
Edit `.env` and configure:
- Database credentials
- App URL
- Mail settings
- Social API keys (if needed)

#### Step 3: Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
npm install
```

#### Step 4: Generate Application Key
```bash
php artisan key:generate
```

#### Step 5: Run Migrations
```bash
php artisan migrate --force
```

#### Step 6: Build Assets
```bash
npm run build
```

#### Step 7: Set Permissions
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Step 8: Configure Web Server
Point your web server document root to the `/public` directory.

## aaPanel Configuration

1. Create a new site in aaPanel
2. Set PHP version to 8.2 or higher
3. Set site root directory to: `/www/wwwroot/yourdomain.com/public`
4. Enable required PHP extensions:
   - bcmath
   - ctype
   - json
   - mbstring
   - openssl
   - pdo
   - tokenizer
   - xml
5. Run the installation wizard or manual installation

## Post-Installation

### Create Admin User (if not using wizard)
```bash
php artisan tinker
>>> $user = new App\Models\User();
>>> $user->name = 'Admin';
>>> $user->email = 'admin@example.com';
>>> $user->password = bcrypt('password');
>>> $user->save();
```

### Create Storage Link
```bash
php artisan storage:link
```

### Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Updating

1. Backup your database and files
2. Upload new files (don't overwrite .env)
3. Run:
```bash
composer install --no-dev
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Troubleshooting

### Permission Issues
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Debug Mode
Enable in `.env` for development:
```
APP_DEBUG=true
```
**Important:** Always set to `false` in production!

## Support
For issues, please check the documentation or contact support.
