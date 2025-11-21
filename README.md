# UniPost - Universal Social Media Management Platform

UniPost is a comprehensive social media management platform built with Laravel 11 that allows users to create, schedule, and publish content across multiple social media platforms from a single interface.

## Features

- 🚀 **Multi-Platform Publishing** - Post to Facebook, Instagram, X (Twitter), TikTok, YouTube, and Pinterest
- 🤖 **AI-Powered Content** - Generate engaging content with OpenAI integration
- 📅 **Smart Scheduling** - Schedule posts for optimal engagement times
- 📊 **Analytics Dashboard** - Track performance across all platforms
- 💳 **Subscription Management** - Flexible pricing plans with Stripe integration
- 👥 **Social Features** - Follow users, like, comment, and repost content
- 🎨 **Platform Previews** - See how your posts will look on each platform
- 🔒 **Secure Authentication** - Email verification and social login support

## Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Vue.js 3 with Vite
- **Admin Panel**: Filament 3
- **Database**: MySQL
- **Queue**: Laravel Queue
- **Payments**: Stripe
- **AI**: OpenAI API
- **Authentication**: Laravel Sanctum & Socialite

## Installation

### Requirements

- PHP 8.2 or higher
- MySQL 5.7+
- Composer
- Node.js 18+ and NPM
- Web server (Apache/Nginx)

### Quick Install via install.php

1. Upload all files to your web server
2. Navigate to `http://yourdomain.com/install.php`
3. Follow the installation wizard
4. After installation, delete `install.php` for security

### Manual Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/msrusu87-web/social-media.git
   cd social-media
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure Database**
   Edit `.env` file with your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=unipost
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Run Migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed Initial Data**
   ```bash
   php artisan db:seed --class=PlanSeeder
   ```

8. **Build Frontend Assets**
   ```bash
   npm run build
   ```

9. **Start Development Server**
   ```bash
   php artisan serve
   npm run dev  # In another terminal
   ```

10. **Access the Application**
    - Main Site: http://localhost:8000
    - Admin Panel: http://localhost:8000/admin

## Configuration

### Social Media API Keys

Add your social media API credentials to `.env`:

```env
# Facebook
FACEBOOK_CLIENT_ID=your_client_id
FACEBOOK_CLIENT_SECRET=your_client_secret

# Twitter/X
TWITTER_API_KEY=your_api_key
TWITTER_API_SECRET=your_api_secret
TWITTER_BEARER_TOKEN=your_bearer_token

# Instagram
INSTAGRAM_CLIENT_ID=your_client_id
INSTAGRAM_CLIENT_SECRET=your_client_secret

# TikTok
TIKTOK_CLIENT_ID=your_client_id
TIKTOK_CLIENT_SECRET=your_client_secret

# YouTube
YOUTUBE_CLIENT_ID=your_client_id
YOUTUBE_CLIENT_SECRET=your_client_secret

# Pinterest
PINTEREST_CLIENT_ID=your_client_id
PINTEREST_CLIENT_SECRET=your_client_secret
```

### Stripe Configuration

```env
STRIPE_KEY=your_publishable_key
STRIPE_SECRET=your_secret_key
STRIPE_WEBHOOK_SECRET=your_webhook_secret
```

### OpenAI Configuration

```env
OPENAI_API_KEY=your_openai_api_key
```

## Project Structure

```
├── app/
│   ├── Filament/           # Admin panel resources
│   │   └── Resources/      # CRUD resources for admin
│   ├── Http/
│   │   └── Controllers/    # Web and API controllers
│   │       ├── API/        # API controllers
│   │       └── Auth/       # Authentication controllers
│   ├── Jobs/               # Background jobs
│   ├── Models/             # Eloquent models
│   └── Services/           # Business logic services
├── config/                 # Configuration files
├── database/
│   ├── migrations/         # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   └── js/
│       ├── Components/     # Vue components
│       └── Pages/          # Vue pages
├── routes/
│   ├── api.php            # API routes
│   └── web.php            # Web routes
└── install.php            # Installation wizard
```

## Key Models

- **User** - Application users with authentication
- **Profile** - User profile information
- **Post** - Social media posts with multi-platform support
- **Comment** - Comments on posts
- **Like** - Likes for posts and comments
- **Repost** - Shared posts
- **Follow** - User following relationships
- **SocialConnection** - Connected social media accounts
- **Plan** - Subscription plans
- **Subscription** - User subscriptions

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user
- `POST /api/auth/logout` - Logout user

### Posts
- `GET /api/posts` - List posts
- `POST /api/posts` - Create post
- `GET /api/posts/{id}` - Get post details
- `PUT /api/posts/{id}` - Update post
- `DELETE /api/posts/{id}` - Delete post

### AI Content
- `POST /api/ai/generate` - Generate content with AI
- `POST /api/ai/hashtags` - Generate hashtags
- `POST /api/ai/improve` - Improve existing content

### Social Connections
- `GET /api/connections` - List connected platforms
- `GET /api/connections/auth-url` - Get OAuth URL
- `POST /api/connections/callback` - Handle OAuth callback
- `DELETE /api/connections/{id}` - Disconnect platform

## Background Jobs

UniPost uses Laravel's queue system for asynchronous tasks:

- **PublishToFacebook** - Publish posts to Facebook
- **PublishToInstagram** - Publish posts to Instagram
- **PublishToX** - Publish posts to X (Twitter)
- **PublishToTikTok** - Publish posts to TikTok
- **PublishToYouTube** - Publish posts to YouTube
- **PublishToPinterest** - Publish posts to Pinterest
- **SendVerificationEmail** - Send email verification

To process jobs:
```bash
php artisan queue:work
```

## Admin Panel

Access the admin panel at `/admin` with an admin account. Features include:

- User management
- Post moderation
- Subscription management
- Plan configuration
- System monitoring

## Security

- All passwords are hashed using bcrypt
- API routes protected with Laravel Sanctum
- CSRF protection on all forms
- SQL injection prevention via Eloquent ORM
- XSS protection through Laravel's Blade templating
- Rate limiting on API endpoints

## License

This project is licensed under the MIT License.

## Support

For support, please visit our documentation or contact support@unipost.app

## Credits

Developed by msrusu87-web

---

**Note**: Remember to configure your web server to point to the `public` directory and set proper file permissions for storage and bootstrap/cache directories.
