# Social Media Management Platform

A modular, multi-lingual social media management platform built with Laravel 11. This platform provides a foundation for managing multiple social media accounts, creating and scheduling posts, and integrating AI-powered features.

## Features

- 🚀 **Guided Web Installer**: Easy setup with step-by-step installation wizard
- 🌍 **Multi-Language Support**: Built-in support for English, Romanian, and Italian
- 🧩 **Modular Architecture**: Extensible design for adding custom modules and plugins
- 🎨 **Theme Support**: Custom theme directory for frontend customization
- 📄 **Basic CMS**: Page management system for static content
- 🤖 **AI Integration Ready**: Placeholder service for future AI features

## Requirements

- PHP >= 8.2
- Composer
- Required PHP Extensions:
  - BCMath
  - Ctype
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML

## Installation

### Option 1: Using the Web Installer (Recommended)

1. Clone the repository:
   ```bash
   git clone https://github.com/msrusu87-web/social-media.git
   cd social-media
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Set proper permissions:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

4. Navigate to `/install` in your browser and follow the installation wizard:
   - **Step 1**: Server requirements check
   - **Step 2**: Database configuration
   - **Step 3**: Run migrations and complete installation

### Option 2: Manual Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/msrusu87-web/social-media.git
   cd social-media
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Configure your database in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. Run migrations:
   ```bash
   php artisan migrate
   ```

7. Create the installation marker:
   ```bash
   touch .installed
   ```

8. Start the development server:
   ```bash
   php artisan serve
   ```

## Project Structure

```
social-media/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Admin/
│   │       │   └── PageController.php    # CMS page management
│   │       └── InstallController.php     # Installation wizard
│   ├── Models/
│   │   └── Page.php                      # Page model for CMS
│   ├── Modules/                          # Custom business logic modules
│   │   └── .gitkeep
│   └── Services/
│       └── AIService.php                 # AI integration placeholder
├── database/
│   └── migrations/
│       └── *_create_pages_table.php      # Pages table migration
├── lang/                                 # Multi-language support
│   ├── en/
│   │   └── messages.php                  # English translations
│   ├── ro/
│   │   └── messages.php                  # Romanian translations
│   └── it/
│       └── messages.php                  # Italian translations
├── resources/
│   └── views/
│       └── installer/                    # Installer views
│           ├── layout.blade.php
│           ├── requirements.blade.php
│           ├── configuration.blade.php
│           ├── install.blade.php
│           └── complete.blade.php
├── routes/
│   └── web.php                          # Application routes
└── themes/                              # Custom themes directory
    └── .gitkeep
```

## Key Components

### 1. Installer System

The installer is accessible at `/install` and provides:

- **Requirements Check**: Validates all necessary PHP extensions
- **Configuration**: Collects database and application settings
- **Installation**: Runs migrations and seeds initial data
- **Auto-disable**: Automatically disabled after successful installation

### 2. Modular Architecture

The `app/Modules` directory is designed for custom business logic modules, such as:
- Social media connectors (Twitter, Facebook, Instagram, LinkedIn)
- Analytics modules
- Scheduling systems
- Content management features

### 3. Multi-Language Support

Language files are located in the `lang` directory with support for:
- English (`en`)
- Romanian (`ro`)
- Italian (`it`)

Add more languages by creating new directories under `lang/`.

### 4. CMS System

The basic CMS includes:
- `pages` database table with title, slug, content, and publication status
- `Page` model with proper relationships and casts
- `Admin\PageController` with CRUD operations (index, create, store, edit, update, destroy)

### 5. AI Service

The `AIService` class provides placeholder methods for future AI integration:

```php
// Format post for specific platform
$aiService->formatPost($text, $platform);

// Generate post suggestions
$aiService->generatePostSuggestion($topic);
```

## Development

### Running Tests

```bash
php artisan test
```

### Code Style

The project uses Laravel Pint for code styling:

```bash
./vendor/bin/pint
```

### Building Assets

```bash
npm run dev        # Development
npm run build      # Production
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Security

If you discover any security-related issues, please email security@example.com instead of using the issue tracker.

## License

This project is licensed under the terms specified in the [LICENSE](LICENSE) file.

## Roadmap

- [ ] User authentication and authorization system
- [ ] Social media account connections
- [ ] Post scheduling and queue management
- [ ] Analytics dashboard
- [ ] AI-powered content suggestions
- [ ] Media library
- [ ] Team collaboration features
- [ ] API for third-party integrations

## Support

For support, please open an issue on GitHub or contact the maintainers.

## Acknowledgments

- Built with [Laravel 11](https://laravel.com)
- Inspired by modern social media management tools
- Community contributions welcome!
