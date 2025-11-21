# UniPost - Universal Social Media Management Platform

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![FilamentPHP](https://img.shields.io/badge/FilamentPHP-3.x-F59E0B?style=flat)](https://filamentphp.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat&logo=vue.js)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-1.x-9553E9?style=flat)](https://inertiajs.com)

UniPost is a comprehensive social media management platform that allows users to manage multiple social media accounts, schedule posts, generate AI-powered content, and analyze engagement across Facebook, Instagram, Twitter/X, and LinkedIn.

## Features

### 🚀 Core Features
- **Multi-Platform Publishing** - Post to Facebook, Instagram, Twitter/X, and LinkedIn simultaneously
- **AI Content Generation** - Generate engaging content with OpenAI integration
- **Post Scheduling** - Schedule posts for optimal engagement times
- **Media Management** - Upload and manage images and videos
- **Real-time Preview** - Preview posts for each platform before publishing

### 💼 Admin Panel (FilamentPHP)
- User management
- Post management and moderation
- Subscription and plan management
- Analytics dashboard
- Social connection monitoring

### 👥 User Features
- Personal feed with posts from followed users
- Explore trending posts
- Follow/unfollow users
- Like, comment, and repost functionality
- Profile customization
- Subscription management

### 🤖 AI-Powered Tools
- Content generation from prompts
- Content improvement suggestions
- Automatic hashtag generation
- Tone and length customization

### 📊 Subscription Plans
- **Free** - 10 posts/month, 1 social account
- **Starter** - 50 posts/month, 3 accounts, AI features
- **Professional** - 200 posts/month, 10 accounts, full analytics
- **Enterprise** - Unlimited posts, 50 accounts, priority support

## Technology Stack

- **Backend**: Laravel 11
- **Frontend**: Vue 3 + Inertia.js
- **Admin Panel**: FilamentPHP 3
- **Database**: MySQL/PostgreSQL
- **Queue**: Redis (recommended for production)
- **Storage**: Local/S3
- **AI**: OpenAI GPT-4

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0+ or PostgreSQL 13+
- Redis (optional, but recommended)

### Using the Installation Script

1. Clone the repository:
```bash
git clone https://github.com/yourusername/unipost.git
cd unipost
```

2. Run the installation script:
```bash
php install.php
```

The installation script will guide you through:
- System requirements check
- Database configuration
- Application settings
- Social media API credentials
- Admin account creation

### Manual Installation

If you prefer manual installation:

1. Install dependencies:
```bash
composer install
npm install
```

2. Copy the environment file:
```bash
cp .env.example .env
```

3. Generate application key:
```bash
php artisan key:generate
```

4. Configure your `.env` file with database and API credentials

5. Run migrations and seeders:
```bash
php artisan migrate --seed
```

6. Build frontend assets:
```bash
npm run build
```

7. Start the development server:
```bash
php artisan serve
```

## Configuration

### Social Media API Keys

#### Facebook/Instagram
1. Create a Facebook App at https://developers.facebook.com
2. Add Facebook Login and Instagram Graph API products
3. Add credentials to `.env`:
```env
FACEBOOK_CLIENT_ID=your_app_id
FACEBOOK_CLIENT_SECRET=your_app_secret
FACEBOOK_REDIRECT_URI=http://yourapp.com/auth/facebook/callback
```

#### Twitter/X
1. Create an app at https://developer.twitter.com
2. Enable OAuth 2.0
3. Add credentials to `.env`:
```env
TWITTER_CLIENT_ID=your_client_id
TWITTER_CLIENT_SECRET=your_client_secret
TWITTER_REDIRECT_URI=http://yourapp.com/auth/twitter/callback
```

#### OpenAI
1. Get your API key from https://platform.openai.com
2. Add to `.env`:
```env
OPENAI_API_KEY=your_api_key
```

### Queue Configuration

For production, configure Redis for queues:

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Run the queue worker:
```bash
php artisan queue:work
```

## Usage

### Creating Your First Post

1. Log in to your account
2. Click "Create Post" on the dashboard
3. Write your content or use AI to generate it
4. Select target platforms
5. Add media (optional)
6. Schedule or publish immediately

### Connecting Social Accounts

1. Go to Settings > Social Connections
2. Click "Connect" for each platform
3. Authorize the application
4. Your account is now connected!

### Using AI Features

#### Generate Content
```javascript
// Click "Generate Content" in the post composer
// Enter a prompt like "Write a professional post about productivity tips"
```

#### Improve Content
```javascript
// Write your draft content
// Click "Improve Content" to enhance it with AI
```

#### Generate Hashtags
```javascript
// After writing your post
// Click "Generate Hashtags" to get relevant tags
```

## API Documentation

### AI Content Endpoints

#### Generate Content
```http
POST /api/ai/generate
Content-Type: application/json
Authorization: Bearer {token}

{
  "prompt": "Write a post about...",
  "tone": "professional|casual|friendly|formal",
  "length": "short|medium|long",
  "platforms": ["facebook", "instagram"]
}
```

#### Improve Content
```http
POST /api/ai/improve
Content-Type: application/json
Authorization: Bearer {token}

{
  "content": "Your existing content...",
  "instruction": "Make it more engaging"
}
```

#### Generate Hashtags
```http
POST /api/ai/hashtags
Content-Type: application/json
Authorization: Bearer {token}

{
  "content": "Your post content...",
  "count": 5
}
```

## File Structure

```
unipost/
├── app/
│   ├── Filament/Resources/    # Admin panel resources
│   ├── Http/Controllers/      # Controllers
│   ├── Jobs/                  # Queue jobs
│   ├── Models/                # Eloquent models
│   └── Services/              # Service classes
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   ├── js/
│   │   ├── Components/        # Vue components
│   │   └── Pages/             # Inertia pages
│   └── views/                 # Blade templates
├── routes/
│   ├── api.php               # API routes
│   └── web.php               # Web routes
└── install.php               # Installation script
```

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

### Compiling Assets
```bash
# Development
npm run dev

# Production
npm run build
```

## Deployment

### Production Checklist

1. Set environment to production:
```env
APP_ENV=production
APP_DEBUG=false
```

2. Optimize the application:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. Set up supervisor for queue workers
4. Configure SSL certificate
5. Set up regular backups
6. Configure logging and monitoring

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email security@unipost.com instead of using the issue tracker.

## License

UniPost is open-sourced software licensed under the [MIT license](LICENSE).

## Support

- Documentation: https://docs.unipost.com
- Issues: https://github.com/yourusername/unipost/issues
- Email: support@unipost.com

## Roadmap

- [ ] LinkedIn integration
- [ ] TikTok support
- [ ] Advanced analytics
- [ ] Team collaboration features
- [ ] Mobile apps (iOS/Android)
- [ ] Content calendar view
- [ ] Bulk post scheduling
- [ ] Post templates
- [ ] Competitor analysis

## Credits

- Laravel Framework - https://laravel.com
- FilamentPHP - https://filamentphp.com
- Vue.js - https://vuejs.org
- Inertia.js - https://inertiajs.com
- Tailwind CSS - https://tailwindcss.com

---

Made with ❤️ by the UniPost Team
