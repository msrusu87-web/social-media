# UniPost - Social Media Management Platform

A comprehensive Laravel 11 application for managing social media accounts and content across multiple platforms.

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL/MariaDB

## Installation

### Quick Install (Recommended)

1. Upload all files to your web server
2. Point your domain to the `/public` directory
3. Visit your domain in a browser
4. The installation wizard will guide you through setup

### Manual Install

1. Clone the repository
2. Copy `.env.example` to `.env`
3. Install dependencies:
   ```bash
   composer install
   npm install
   ```
4. Generate application key:
   ```bash
   php artisan key:generate
   ```
5. Run migrations:
   ```bash
   php artisan migrate
   ```
6. Build assets:
   ```bash
   npm run build
   ```

## Features

- Multi-platform social media management
- Content scheduling and publishing
- Analytics and reporting
- User management with Filament admin panel
- API integration with major social platforms

## Configuration

Edit the `.env` file to configure:
- Database connection
- Social media API keys
- Mail settings
- Other application settings

## aaPanel Setup

For aaPanel hosting:
1. Create a new site in aaPanel
2. Point the site root to the `/public` directory
3. Enable PHP 8.2+
4. Run the installation wizard

## License

MIT License - see LICENSE file for details
