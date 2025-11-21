# UniPost - Unified Social Media Management Platform

UniPost is a comprehensive social media management platform built with Laravel 11 that allows you to manage multiple social media accounts from a single dashboard.

## Features

- 📝 **Multi-Platform Posting**: Publish to Facebook, Instagram, X (Twitter), TikTok, YouTube, and Pinterest
- 🤖 **AI Content Generation**: Generate engaging content and hashtags using AI
- 📅 **Post Scheduling**: Schedule posts for optimal engagement times
- 📊 **Analytics Dashboard**: Track performance across all platforms
- 👥 **Team Collaboration**: Work with your team on content creation
- 💳 **Subscription Management**: Flexible pricing plans with Stripe integration
- 🔐 **Social Authentication**: Login with social media accounts
- 🎨 **Platform Previews**: Preview how your posts will look on each platform
- 📱 **Responsive Design**: Works on desktop, tablet, and mobile devices

## Requirements

- PHP 8.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Node.js 18+ and NPM
- aaPanel (optional, for easy deployment)

## Installation

### Quick Installation with install.php

1. Upload all files to your web server
2. Navigate to `http://yourdomain.com/install.php`
3. Follow the installation wizard:
   - Check system requirements
   - Configure database connection
   - Set up application settings
   - Configure social media API keys
   - Complete installation

### Manual Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/unipost.git
   cd unipost
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install
   ```

4. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=unipost
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```

8. Build frontend assets:
   ```bash
   npm run build
   ```

9. Create storage symlink:
   ```bash
   php artisan storage:link
   ```

10. Start the development server:
    ```bash
    php artisan serve
    ```

## Configuration

### Social Media API Keys

Configure your social media API credentials in the `.env` file:

```env
# Facebook
FACEBOOK_CLIENT_ID=your_facebook_app_id
FACEBOOK_CLIENT_SECRET=your_facebook_app_secret

# Instagram (uses Facebook API)
# Use the same credentials as Facebook

# Twitter/X
TWITTER_CLIENT_ID=your_twitter_client_id
TWITTER_CLIENT_SECRET=your_twitter_client_secret

# TikTok
TIKTOK_CLIENT_ID=your_tiktok_client_key
TIKTOK_CLIENT_SECRET=your_tiktok_client_secret

# YouTube (uses Google API)
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret

# Pinterest
PINTEREST_CLIENT_ID=your_pinterest_app_id
PINTEREST_CLIENT_SECRET=your_pinterest_app_secret
```

### OpenAI Configuration

For AI content generation features:

```env
OPENAI_API_KEY=your_openai_api_key
```

### Stripe Configuration

For subscription management:

```env
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret
```

## Usage

### Creating a Post

1. Navigate to the Dashboard
2. Click "Create Post"
3. Write your content or use AI to generate it
4. Add media (images/videos)
5. Select target platforms
6. Choose to publish immediately or schedule for later
7. Click "Publish" or "Schedule"

### Connecting Social Accounts

1. Go to Settings → Connected Accounts
2. Click "Connect" for each platform you want to use
3. Authorize the application
4. Your account will be connected and ready to use

### Managing Subscriptions

1. Go to Settings → Subscription
2. Choose a plan that fits your needs
3. Enter payment information
4. Activate your subscription

## Development

### Running Tests

```bash
php artisan test
```

### Code Style

This project follows PSR-12 coding standards. Run PHP CS Fixer:

```bash
./vendor/bin/pint
```

### Building Assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

## Deployment

### aaPanel Deployment

1. Install aaPanel on your server
2. Create a new site in aaPanel
3. Upload files to the site directory
4. Set document root to `/public`
5. Configure SSL certificate
6. Run installation via `install.php`

### Traditional Deployment

1. Upload files to your server
2. Point web server to `/public` directory
3. Set proper permissions:
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```
4. Configure web server (Apache/Nginx)
5. Set up queue worker:
   ```bash
   php artisan queue:work --daemon
   ```
6. Set up scheduler cron job:
   ```
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```

## Security

- All API keys are stored securely in `.env`
- User passwords are hashed using bcrypt
- CSRF protection enabled on all forms
- SQL injection protection via Eloquent ORM
- XSS protection via Blade templating

## Support

For support, email support@unipost.com or join our Discord community.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Credits

- Built with [Laravel 11](https://laravel.com)
- UI components with [Vue 3](https://vuejs.org)
- Admin panel with [Filament](https://filamentphp.com)
- Icons from [Heroicons](https://heroicons.com)

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request
