# Realtor SaaS Platform

A multi-tenant platform for realtors to create and manage their own branded websites. Built with Laravel 11, Filament v3, and Stripe.

---

## Features

* **Multi-Tenant**: Each realtor gets a subdomain (`johnsmith.myrealtorsites.com`)
* **Admin Panels**: Super Admin (`/admin`) & Tenant Dashboard (`/dashboard`)
* **Property Management**: Add, edit, list properties with images
* **Client Testimonials & Contact Form**: Capture leads and reviews
* **Stripe Billing**: Subscriptions with 14-day trial
* **Templates**: Modern, Professional, Luxury
* **SEO Ready**

---

## Tech Stack

* Laravel 11, PHP 8.3
* Filament v3 (Admin Panels)
* PostgreSQL 15, Redis
* Stripe for payments
* Blade, TailwindCSS, Alpine.js
* Docker & Docker Compose

---

## Quick Start (Docker)

```bash
git clone https://github.com/your-org/realtor-saas.git
cd realtor-saas
cp .env.example .env   # Configure database, Stripe, etc.
docker-compose up -d
docker-compose exec app php artisan migrate --seed
```

* Admin: `http://localhost/admin`
* Tenant: `http://localhost/dashboard`

---

## Default Accounts

| Role        | Email                                         | Password |
| ----------- | --------------------------------------------- | -------- |
| Super Admin | [admin@example.com](mailto:admin@example.com) | password |
| Demo Tenant | [demo@example.com](mailto:demo@example.com)   | password |

> Change in production.

---

## Deployment

```bash
./scripts/deploy.sh production   # One-step deploy
# or manually:
composer install --no-dev
npm run build
php artisan migrate --force
php artisan config:cache
php artisan queue:restart
```

---

## Notes

* Stripe webhooks: `/api/webhooks/stripe`
* Subdomains: Use wildcard DNS `*.myrealtorsites.com`
* Queue workers: `php artisan queue:work redis`

---

## License

CC BY-NC 4.0 — non-commercial only
