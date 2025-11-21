# Realtor SaaS Platform

A multi-tenant SaaS platform for real estate professionals to create and manage their own branded websites. Built with Laravel 11, Filament v3, and Stripe for subscription billing.

## Features

- **Multi-Tenant Architecture**: Each realtor gets their own subdomain (e.g., `johnsmith.myrealtorsites.com`)
- **Dual Admin Panels**:
  - Super Admin panel for platform management (`/admin`)
  - Tenant dashboard for realtors (`/dashboard`)
- **Property Management**: Full CRUD for property listings with images, features, and filtering
- **Client Testimonials**: Manage and display client reviews
- **Contact Form**: Capture leads with email notifications
- **Subscription Billing**: Stripe integration with 14-day trial
- **3 Professional Templates**: Modern, Professional, and Luxury themes
- **SEO Optimization**: Meta tags, Open Graph, and customizable settings

## Tech Stack

- **Backend**: Laravel 11 (PHP 8.3)
- **Admin Panels**: Filament v3
- **Database**: PostgreSQL 15
- **Cache/Queue**: Redis
- **Payments**: Stripe
- **Frontend**: Blade templates, TailwindCSS, Alpine.js
- **Containerization**: Docker & Docker Compose

## Requirements

- PHP 8.3+
- Composer 2.x
- Node.js 18+ & NPM
- PostgreSQL 15+
- Redis 7+
- Stripe account

## Quick Start

### Using Docker (Recommended)

```bash
# Clone the repository
git clone https://github.com/your-org/realtor-saas.git
cd realtor-saas

# Copy environment file
cp .env.example .env

# Configure your environment variables (database, Stripe, etc.)
nano .env

# Start Docker containers
docker-compose up -d

# Run migrations and seed data
docker-compose exec app php artisan migrate --seed

# Access the application
# Admin Panel: http://localhost/admin
# Tenant Dashboard: http://localhost/dashboard
```

### Manual Installation

```bash
# Clone and initialize
git clone https://github.com/your-org/realtor-saas.git
cd realtor-saas
./scripts/init.sh

# Configure environment
cp .env.example .env
# Edit .env with your database and Stripe credentials

# Run migrations
php artisan migrate --seed

# Start development server
php artisan serve
```

## Configuration

### Environment Variables

Key environment variables to configure:

```env
# Application
APP_URL=http://localhost
APP_BASE_DOMAIN=myrealtorsites.com

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=realtor_saas
DB_USERNAME=postgres
DB_PASSWORD=secret

# Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Stripe
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx
STRIPE_PRICE_ID=price_xxx

# Mail (for contact form notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

### Stripe Setup

1. Create a Stripe account at https://stripe.com
2. Create a Product with a recurring Price ($39/month recommended)
3. Copy your API keys and Price ID to `.env`
4. Set up a webhook endpoint pointing to `/api/webhooks/stripe`
5. Subscribe to these webhook events:
   - `checkout.session.completed`
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
   - `customer.subscription.trial_will_end`

### Subdomain Configuration

For production, configure your DNS with a wildcard A record:

```
*.myrealtorsites.com -> YOUR_SERVER_IP
```

Update Nginx configuration (included in `docker/nginx/default.conf`) to handle wildcard subdomains.

## Default Credentials

After running seeders:

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@example.com | password |
| Demo Tenant | demo@example.com | password |

**Important**: Change these in production!

## Architecture

### Database Schema

- `users` - Platform users (admins and tenants)
- `subscriptions` - Stripe subscription records
- `templates` - Website theme templates
- `sites` - Tenant website configurations
- `properties` - Real estate listings
- `property_images` - Property photos
- `testimonials` - Client reviews
- `contact_submissions` - Lead capture

### Multi-Tenancy

Uses single-database multi-tenancy with:
- `user_id` foreign key on tenant data tables
- Global scopes for automatic tenant filtering
- Middleware for subdomain resolution

### Directory Structure

```
app/
├── Filament/
│   ├── Admin/           # Super admin panel resources
│   │   ├── Resources/
│   │   └── Widgets/
│   └── Tenant/          # Tenant dashboard resources
│       ├── Resources/
│       ├── Pages/
│       └── Widgets/
├── Http/
│   ├── Controllers/
│   │   ├── PublicSiteController.php
│   │   └── Webhooks/
│   └── Middleware/
│       ├── TenantMiddleware.php
│       └── EnsureSubscriptionActive.php
├── Mail/
├── Models/
└── Providers/
    └── Filament/
        ├── AdminPanelProvider.php
        └── TenantPanelProvider.php

resources/views/
├── templates/
│   ├── modern/          # Modern theme
│   ├── professional/    # Professional theme
│   └── luxury/          # Luxury theme
├── filament/
└── emails/

docker/
├── nginx/
└── php/
```

## API Endpoints

### Webhooks

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/webhooks/stripe` | POST | Stripe webhook handler |

### Public Routes (Tenant Sites)

| Route | Description |
|-------|-------------|
| `/` | Homepage |
| `/properties` | Property listings |
| `/properties/{slug}` | Property detail |
| `/about` | About page |
| `/contact` | Contact form |

## Deployment

### Using the Deploy Script

```bash
./scripts/deploy.sh production
```

This script:
1. Pulls latest changes
2. Installs dependencies
3. Builds assets
4. Runs migrations
5. Clears and rebuilds caches
6. Restarts queue workers

### Manual Deployment

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

### Queue Workers

For processing emails and background jobs:

```bash
# Using Supervisor (recommended)
php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600

# Or using Docker
docker-compose up -d queue
```

## Testing

```bash
# Run tests
php artisan test

# With coverage
php artisan test --coverage
```

## Customization

### Adding a New Template

1. Create directory: `resources/views/templates/your-template/`
2. Add required files:
   - `layouts/app.blade.php`
   - `partials/nav.blade.php`
   - `partials/footer.blade.php`
   - `partials/property-card.blade.php`
   - `home.blade.php`
   - `properties.blade.php`
   - `property.blade.php`
   - `about.blade.php`
   - `contact.blade.php`
3. Add template record to database via seeder or admin panel

### Extending Filament Resources

See Filament documentation: https://filamentphp.com/docs

## Troubleshooting

### Common Issues

**Subdomain not resolving locally**
- Add entries to `/etc/hosts`: `127.0.0.1 johnsmith.localhost`
- Or use query parameter: `http://localhost?subdomain=johnsmith`

**Stripe webhooks failing**
- Ensure webhook secret is correct in `.env`
- Check webhook endpoint is accessible
- Review logs: `storage/logs/laravel.log`

**Queue jobs not processing**
- Verify Redis connection
- Check queue worker is running
- Review failed jobs: `php artisan queue:failed`

## License

Proprietary - All rights reserved.

## Support

For support, please contact support@myrealtorsites.com
