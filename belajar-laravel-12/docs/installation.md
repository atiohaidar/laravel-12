# Installation Guide

## Prerequisites

Before you begin, ensure you have the following installed:

- PHP 8.2 or higher
- Composer
- SQLite (for local development and testing)
- Postman (for API testing)

## Installation Steps

1. Clone the repository:
```bash
git clone <repository-url>
cd belajar-laravel-12
```

2. Install dependencies:
```bash
composer install
```

3. Set up your environment file:
```bash
cp .env.example .env
```

4. Generate an application key:
```bash
php artisan key:generate
```

5. Run database migrations:
```bash
php artisan migrate
```

6. Start the development server:
```bash
php artisan serve
```

The API will be accessible at `http://localhost:8000`

## Environment Configuration

Make sure to configure the following in your `.env` file:

- `APP_URL`: Your application URL
- `DB_CONNECTION`: Database connection (default: sqlite)
- `SANCTUM_STATEFUL_DOMAINS`: Domains allowed for stateful authentication
- `SESSION_DOMAIN`: Cookie domain for session handling
