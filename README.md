# EcoWatch - Environmental Violation Reporting System

A comprehensive web-based environmental violation reporting and tracking system built with Laravel 12 for Local Government Units (LGUs) in Davao del Norte, Philippines.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [Testing](#testing)
- [User Roles](#user-roles)
- [External Libraries](#external-libraries)
- [Project Structure](#project-structure)
- [Documentation](#documentation)
- [License](#license)

---

## Overview

EcoWatch is a full-stack environmental violation reporting platform that enables:
- Citizens to report environmental violations (authenticated or anonymously)
- Local Government Units to track and resolve reported issues
- Administrators to validate reports and manage the system
- Community engagement through a public feed with upvoting and flagging

The system features GPS-based auto-assignment of reports to the nearest LGU, photo evidence uploads, status tracking, notifications, and comprehensive analytics dashboards.

---

## Features

### Core Features
- Multi-role system (Admin, LGU Staff, Regular Users, Anonymous)
- Authenticated and anonymous report submission
- GPS-based automatic assignment to nearest LGU
- Photo evidence upload and management
- Report status workflow (pending → in-review → in-progress → awaiting-confirmation → resolved)
- Public feed with community upvoting and flagging
- Real-time notification system
- Admin report validation and verification
- LGU resolution tracking with proof photos
- User confirmation of report resolution
- Analytics dashboards with Chart.js visualizations
- LGU and admin announcement system

### Advanced Features
- Geographic coverage radius calculations
- Report validity tracking and dispute resolution
- Progress timeline and update logging
- Priority classification (low, medium, high, urgent)
- IP-based upvote tracking for anonymous users
- Report flagging for community moderation
- Custom pagination for public feed
- Role-based access control
- Notification center with read/unread status

---

## Technology Stack

### Backend
- **Framework:** Laravel 12
- **Language:** PHP 8.2+
- **Database:** MySQL 8.0+
- **ORM:** Eloquent
- **Authentication:** Laravel Auth with Session-based authentication

### Frontend
- **Template Engine:** Blade (Laravel)
- **CSS Framework:** Bootstrap 5.2.3
- **JavaScript:** Vanilla JavaScript + jQuery (via Bootstrap)
- **Charts:** Chart.js (via CDN)
- **Maps:** Leaflet.js + OpenStreetMap (via CDN)
- **Icons:** Font Awesome (via CDN)
- **Future:** Vue.js 3.2 (included for future enhancements)

### Build Tools
- **Asset Bundler:** Vite 7.0
- **CSS Preprocessor:** SASS 1.56
- **Additional:** Tailwind CSS 4.0 (utility-first CSS)

### Development Tools
- **Package Manager (PHP):** Composer 2.x
- **Package Manager (JS):** NPM 9.x+
- **Testing:** Pest PHP
- **Code Style:** Laravel Pint
- **Local Server:** Laragon (Windows) or Laravel Sail (Docker)

---

## Requirements

### System Requirements
- **PHP:** 8.2 or higher
- **MySQL:** 8.0 or higher
- **Node.js:** 18.x or higher
- **NPM:** 9.x or higher
- **Composer:** 2.x or higher

### PHP Extensions
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- GD (for image processing)

### Recommended Development Environment
- **Windows:** Laragon (includes Apache, MySQL, PHP, Node.js)
- **Mac/Linux:** Laravel Sail (Docker-based)
- **Alternative:** XAMPP, WAMP, MAMP, or Valet

---

## Installation

### Step 1: Clone the Repository
```bash
git clone <repository-url> ecowatch
cd ecowatch
```

### Step 2: Install PHP Dependencies
```bash
composer install
```

### Step 3: Install JavaScript Dependencies
```bash
npm install
```

### Step 4: Environment Configuration
```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 5: Configure Database
Edit the `.env` file and set your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecowatch_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 6: Create Database
Create a new MySQL database named `ecowatch_db` (or your chosen name):
```sql
CREATE DATABASE ecowatch_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 7: Run Migrations
```bash
php artisan migrate
```

### Step 8: Seed Database (Optional)
```bash
php artisan db:seed
```

This will create:
- Default admin user
- Sample LGUs and barangays
- Violation types
- Sample reports (for testing)

### Step 9: Create Storage Symlink
```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public` for file uploads.

### Step 10: Build Frontend Assets
```bash
# For development (with hot reload)
npm run dev

# For production (optimized build)
npm run build
```

---

## Configuration

### Environment Variables

Key environment variables in `.env`:

```env
# Application
APP_NAME="EcoWatch"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecowatch_db
DB_USERNAME=root
DB_PASSWORD=

# Mail (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@ecowatch.local"
MAIL_FROM_NAME="${APP_NAME}"

# File Upload
FILESYSTEM_DISK=public
```

### File Upload Configuration
Edit `config/filesystems.php` if needed. Default upload location: `storage/app/public/uploads/reports/`

### Database Seeding
Default admin credentials (after seeding):
- Email: `admin@ecowatch.local`
- Password: `password`

---

## Running the Application

### Method 1: Using PHP Artisan Serve (Development)
```bash
# Start the Laravel development server
php artisan serve

# In a separate terminal, start Vite
npm run dev

# Access the application at:
# http://localhost:8000
```

### Method 2: Using Laragon (Windows)
1. Place project folder in `C:\laragon\www\ecowatch-f`
2. Start Laragon
3. Access via `http://ecowatch-f.test`

### Method 3: Using Laravel Sail (Docker)
```bash
# Start Sail containers
./vendor/bin/sail up

# Run migrations
./vendor/bin/sail artisan migrate

# Access at http://localhost
```

### Method 4: Using Composer Script (All-in-one)
```bash
# Run server, queue, and Vite concurrently
composer dev
```

This command runs:
- `php artisan serve` (web server)
- `php artisan queue:listen` (background jobs)
- `npm run dev` (Vite development server)

---

## Testing

### Run All Tests
```bash
# Using Composer
composer test

# Using Artisan
php artisan test

# Using Pest directly
./vendor/bin/pest
```

### Run Specific Test Suite
```bash
# Feature tests
php artisan test --testsuite=Feature

# Unit tests
php artisan test --testsuite=Unit
```

### Code Style Formatting
```bash
# Fix code style issues
./vendor/bin/pint
```

---

## User Roles

### 1. Admin
**Capabilities:**
- Full system access
- Validate and verify reports
- Manage users (create, edit, delete, toggle status)
- Adjust report priority
- Delete reports
- Publish system-wide announcements
- View all analytics and dashboards
- Manage violation types and LGUs

**Login:** `admin@ecowatch.local` (after seeding)

### 2. LGU Staff
**Capabilities:**
- View assigned reports (within coverage area)
- Mark reports as in-progress
- Upload proof photos and mark as fixed
- Publish local announcements
- View LGU-specific analytics
- Update profile and password

**Assignment:** Admin creates LGU users and assigns them to specific LGUs

### 3. Regular Users
**Capabilities:**
- Submit authenticated reports
- Upload evidence photos
- Track report status
- Confirm or reject LGU resolutions
- View personal dashboard
- Receive notifications
- Update profile and password
- Upvote and flag reports on public feed

**Registration:** Self-registration via `/register`

### 4. Anonymous/Public
**Capabilities:**
- Submit anonymous reports (requires admin verification)
- View public feed
- Upvote reports (IP-based tracking)
- Check report status by report ID
- View public announcements

**Access:** No registration required

---

## External Libraries

### Backend (Composer Packages)

#### Core Framework
1. **laravel/framework ^12.0** - Full-stack PHP framework
2. **laravel/tinker ^2.10** - Interactive REPL for Laravel
3. **laravel/ui ^4.6** - Authentication scaffolding and frontend presets

#### Development & Testing
4. **fakerphp/faker ^1.23** - Generate fake data for testing
5. **pestphp/pest ^3.8** - Modern testing framework
6. **pestphp/pest-plugin-laravel ^3.2** - Laravel integration for Pest
7. **laravel/pint ^1.24** - Opinionated PHP code style fixer
8. **laravel/sail ^1.41** - Docker-based development environment
9. **laravel/boost ^1.8** - Performance optimization package
10. **laravel/pail ^1.2** - Real-time log viewer
11. **mockery/mockery ^1.6** - Mocking library for tests
12. **nunomaduro/collision ^8.6** - Beautiful error reporting

### Frontend (NPM Packages)

#### UI Frameworks
1. **bootstrap ^5.2.3** - Responsive CSS framework for layout and components
2. **@popperjs/core ^2.11.6** - Tooltip and popover positioning engine (Bootstrap dependency)
3. **tailwindcss ^4.0.0** - Utility-first CSS framework (for future use)

#### JavaScript Libraries
4. **axios ^1.11.0** - Promise-based HTTP client for AJAX requests
5. **vue ^3.2.37** - Progressive JavaScript framework (for future SPA features)

#### Build Tools
6. **vite ^7.0.7** - Next-generation frontend build tool
7. **laravel-vite-plugin ^2.0.0** - Laravel integration for Vite
8. **@vitejs/plugin-vue ^6.0.0** - Vue.js support for Vite
9. **@tailwindcss/vite ^4.0.0** - Tailwind CSS integration for Vite
10. **sass ^1.56.1** - CSS preprocessor with SCSS syntax
11. **concurrently ^9.0.1** - Run multiple npm scripts simultaneously

### Third-Party CDN Libraries

#### Charts & Visualization
1. **Chart.js 4.x** (CDN) - Interactive charts for analytics dashboards
   - Used for: Bar charts, line charts, doughnut charts
   - Location: Included via CDN in dashboard layouts

#### Maps & Geolocation
2. **Leaflet.js 1.9.x** (CDN) - Interactive map library
   - Used for: Report location selection, map display
   - Tile Provider: OpenStreetMap
   - Location: Included via CDN in report forms

#### Icons & Typography
3. **Font Awesome 6.x** (CDN) - Icon font library
   - Used for: UI icons throughout the application
   - Location: Included via CDN in layouts

#### JavaScript Utilities
4. **jQuery 3.x** - JavaScript library (included with Bootstrap)
   - Note: Primary usage is through Bootstrap components
   - Modern vanilla JavaScript is preferred for new features

---

## Project Structure

```
ecowatch-f/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Application controllers
│   │   ├── Middleware/           # HTTP middleware
│   │   └── Requests/             # Form request validation
│   ├── Models/                   # Eloquent models
│   └── Providers/                # Service providers
│
├── bootstrap/
│   └── app.php                   # Application bootstrap
│
├── config/                       # Configuration files
│   ├── app.php
│   ├── database.php
│   └── filesystems.php
│
├── database/
│   ├── factories/                # Model factories
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
│
├── public/
│   ├── css/                      # Compiled CSS
│   ├── js/                       # Custom JavaScript files
│   │   ├── location-dropdowns.js
│   │   ├── map-helper.js
│   │   ├── map-lightbox.js
│   │   ├── modal-helper.js
│   │   ├── report-map.js
│   │   └── table-filter.js
│   ├── uploads/                  # User uploaded files
│   │   └── reports/              # Report photos
│   └── index.php                 # Application entry point
│
├── resources/
│   ├── css/
│   │   └── app.scss              # Main stylesheet
│   ├── js/
│   │   ├── app.js                # Main JavaScript
│   │   └── bootstrap.js          # Bootstrap/Axios setup
│   └── views/
│       ├── auth/                 # Dashboard views
│       ├── components/           # Reusable components
│       ├── layouts/              # Layout templates
│       ├── settings/             # Settings pages
│       └── notifications/        # Notification views
│
├── routes/
│   ├── web.php                   # Web routes
│   ├── api.php                   # API routes
│   └── console.php               # Console routes
│
├── storage/
│   ├── app/
│   │   └── public/               # Public file storage
│   ├── framework/                # Framework cache/sessions
│   └── logs/                     # Application logs
│
├── tests/
│   ├── Feature/                  # Feature tests
│   └── Unit/                     # Unit tests
│
├── .env                          # Environment configuration
├── .env.example                  # Example environment file
├── artisan                       # Artisan CLI
├── composer.json                 # PHP dependencies
├── package.json                  # JavaScript dependencies
├── vite.config.js                # Vite configuration
├── tailwind.config.js            # Tailwind CSS configuration
├── README.md                     # This file
└── SYSTEM-OVERVIEW.md            # Detailed system documentation
```

---

## Documentation

### Available Documentation Files

1. **README.md** (this file) - Setup and installation guide
2. **SYSTEM-OVERVIEW.md** - Comprehensive system documentation including:
   - Detailed controller descriptions
   - Complete blade view catalog
   - JavaScript file functions
   - Database table schemas and relationships
   - Feature workflows and data flows
   - External library details

### Additional Resources

- **Laravel Documentation:** https://laravel.com/docs/12.x
- **Bootstrap Documentation:** https://getbootstrap.com/docs/5.2/
- **Chart.js Documentation:** https://www.chartjs.org/docs/
- **Leaflet Documentation:** https://leafletjs.com/reference.html

---

## Common Tasks

### Create a New Admin User
```bash
php artisan tinker

# In Tinker:
$user = new App\Models\User();
$user->name = 'Admin Name';
$user->email = 'admin@example.com';
$user->password = Hash::make('password');
$user->role = 'admin';
$user->is_active = true;
$user->save();
```

### Create LGU Staff User
```bash
php artisan tinker

# In Tinker:
$user = new App\Models\User();
$user->name = 'LGU Staff Name';
$user->email = 'lgu@example.com';
$user->password = Hash::make('password');
$user->role = 'lgu';
$user->lgu_id = 1; // Assign to specific LGU
$user->is_active = true;
$user->save();
```

### Clear Cache
```bash
# Clear all cache
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear
```

### Generate New Migration
```bash
php artisan make:migration create_table_name
```

### Generate New Controller
```bash
php artisan make:controller ControllerName
```

### Generate New Model
```bash
php artisan make:model ModelName -m  # -m flag creates migration
```

---

## Troubleshooting

### Issue: "419 Page Expired" Error
**Solution:** Clear browser cache or regenerate session
```bash
php artisan session:flush
php artisan cache:clear
```

### Issue: Storage Link Not Working
**Solution:** Recreate storage symlink
```bash
# Remove old link (Windows)
rmdir public\storage

# Recreate link
php artisan storage:link
```

### Issue: Permission Denied on Storage/Logs
**Solution:** Set proper permissions (Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
```

### Issue: Vite Not Loading Assets
**Solution:** Ensure Vite dev server is running
```bash
npm run dev
```

### Issue: Database Connection Error
**Solution:** Check database credentials in `.env` and ensure MySQL service is running

---

## Production Deployment

### Pre-Deployment Checklist

1. Set environment to production in `.env`:
```env
APP_ENV=production
APP_DEBUG=false
```

2. Build production assets:
```bash
npm run build
```

3. Optimize Laravel:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

4. Set proper file permissions:
```bash
# Linux/Mac
chmod -R 755 storage bootstrap/cache
```

5. Set up queue worker (for background jobs):
```bash
# Using supervisor or systemd
php artisan queue:work --daemon
```

6. Set up scheduled tasks (for maintenance):
```bash
# Add to crontab
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Security Considerations

1. **Change Default Admin Credentials** - Immediately after seeding
2. **Set Strong APP_KEY** - Use `php artisan key:generate`
3. **Disable Debug Mode** - Set `APP_DEBUG=false` in production
4. **Use HTTPS** - Always use SSL certificates in production
5. **Regular Updates** - Keep Laravel and dependencies updated
6. **File Upload Validation** - Limit file types and sizes
7. **Input Sanitization** - Use Laravel's built-in validation
8. **CSRF Protection** - Enabled by default in Laravel
9. **SQL Injection Prevention** - Use Eloquent ORM and query builder
10. **Rate Limiting** - Configure in `app/Http/Kernel.php`

---

## Support

For technical issues or questions:
1. Review the [SYSTEM-OVERVIEW.md](SYSTEM-OVERVIEW.md) documentation
2. Check Laravel documentation: https://laravel.com/docs
3. Review inline code comments
4. Contact system administrator

---

## License

This project is licensed under the MIT License.

---

## Credits

**Developed for:** Local Government Units of Davao del Norte, Philippines

**Framework:** Laravel 12 by Taylor Otwell and the Laravel community

**Libraries:**
- Bootstrap by Twitter
- Chart.js by Chart.js Contributors
- Leaflet by Vladimir Agafonkin
- Font Awesome by Fonticons, Inc.

---

## Changelog

### Recent Updates
- Implemented report flagging system (removed downvote feature)
- Enhanced user and LGU dashboards with improved filtering
- Added admin settings panel for user management
- Implemented announcement system for LGUs and admins
- Added comprehensive analytics with Chart.js
- Improved public feed with upvote system
- Database schema documentation cleanup

---

**Last Updated:** December 10, 2025
**Version:** 1.0.0
