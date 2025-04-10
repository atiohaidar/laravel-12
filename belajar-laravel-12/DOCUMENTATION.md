# Laravel 12 API Documentation

> **Note**: The documentation has been moved to the `/docs` folder for better organization.

## Available Documentation

- [Installation Guide](/docs/installation.md) - Setup and installation instructions
- [API Documentation](/docs/api.md) - Complete API endpoints reference
- [Development Guide](/docs/development.md) - Development workflow and best practices
- [Testing Guide](/docs/testing.md) - Testing procedures and examples
- [Troubleshooting](/docs/troubleshooting.md) - Common issues and solutions

Please visit the [documentation index](/docs/README.md) for the complete documentation.
5. [Authentication](#authentication)
6. [User Management](#user-management)
7. [Testing](#testing)
8. [Postman Collection](#postman-collection)
9. [Common Issues and Solutions](#common-issues-and-solutions)

## Project Overview

This project is a RESTful API built with Laravel 12, using Laravel Sanctum for API authentication. It provides endpoints for user registration, authentication, and user management (CRUD operations).

## Prerequisites

Before you begin, ensure you have the following installed:

- PHP 8.2 or higher
- Composer
- SQLite (for local development and testing)
- Postman (for API testing)

## Getting Started

### Installation

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

The API will be accessible at `http://localhost:8000`.

## API Structure

The API is structured around two main components:

1. **Authentication** - Managing user registration, login, and logout
2. **User Management** - CRUD operations for users

## Authentication

Authentication is implemented using Laravel Sanctum, which provides a simple way to authenticate single-page applications (SPAs), mobile applications, and simple token-based APIs.

### Available Endpoints

| Method | Endpoint       | Description              | Auth Required |
|--------|---------------|--------------------------|--------------|
| POST   | /api/register | Register a new user      | No           |
| POST   | /api/login    | Login and get token      | No           |
| POST   | /api/logout   | Invalidate token         | Yes          |
| GET    | /api/user     | Get authenticated user   | Yes          |

### Example Registration Request

```json
POST /api/register
{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

### Example Login Request

```json
POST /api/login
{
    "email": "test@example.com",
    "password": "password"
}
```

Upon successful login, you will receive a token that should be included in the Authorization header for subsequent requests:

```
Authorization: Bearer <your_token>
```

## User Management

The API provides endpoints for managing users. All user management endpoints require authentication.

### Available Endpoints

| Method | Endpoint         | Description         | Auth Required |
|--------|-----------------|---------------------|--------------|
| GET    | /api/users      | Get all users       | Yes          |
| GET    | /api/users/{id} | Get user by ID      | Yes          |
| POST   | /api/users      | Create new user     | Yes          |
| PUT    | /api/users/{id} | Update user by ID   | Yes          |
| DELETE | /api/users/{id} | Delete user by ID   | Yes          |

### Example Create User Request

```json
POST /api/users
{
    "name": "New User",
    "email": "new.user@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

### Example Update User Request

```json
PUT /api/users/1
{
    "name": "Updated Name",
    "email": "updated.email@example.com"
}
```

## Testing

The project includes comprehensive test coverage for API functionality. Tests are written using PHPUnit and are located in the `tests/Feature` directory.

### Running Tests

To run all tests:
```bash
php artisan test
```

To run specific test files:
```bash
php artisan test --filter=ApiAuthTest
php artisan test --filter=ApiUserTest
```

To run specific test methods:
```bash
php artisan test --filter=test_user_can_register_api
```

## Database Structure

The database contains the following main tables:

1. **users** - Stores user information
   - id (primary key)
   - name
   - email
   - password (hashed)
   - created_at
   - updated_at

2. **personal_access_tokens** - Stores API tokens (created by Sanctum)
   - id (primary key)
   - tokenable_type
   - tokenable_id
   - name
   - token (hashed)
   - abilities
   - last_used_at
   - expires_at
   - created_at
   - updated_at

## Postman Collection

A Postman collection is included to help you test the API endpoints. The collection is available in the root directory as `Laravel_12_API.postman_collection.json`.

### How to Use the Postman Collection

1. Import the collection into Postman
2. Set up an environment with the correct `base_url` (default is http://localhost:8000)
3. Run the Register or Login request first to get an authentication token
4. The token is automatically saved to the `auth_token` variable
5. All subsequent authenticated requests will use this token

## Authentication Flow

1. User registers or logs in and receives a token
2. Token is stored by the client application
3. Subsequent requests include the token in the Authorization header
4. When the user logs out, the token is invalidated

## Error Handling

The API returns appropriate HTTP status codes and error messages:

| Status Code | Description                 |
|-------------|-----------------------------|
| 200         | Success                     |
| 201         | Resource created            |
| 401         | Unauthorized                |
| 422         | Validation error            |
| 500         | Server error                |

## Common Issues and Solutions

### Issue: Token Not Working

**Solution**: Ensure you're including the token correctly in the Authorization header as `Bearer <token>`. Also, check if the token has expired or has been invalidated by a logout.

### Issue: CORS Errors

**Solution**: If you're accessing the API from a different domain, ensure CORS is properly configured in `config/cors.php`.

### Issue: Database Errors

**Solution**: Make sure your database configuration in `.env` is correct. For local development, the project is configured to use SQLite.

## Project Structure

```
belajar-laravel-12/
    app/
        Http/
            Controllers/
                Api/
                    AuthController.php    # Handles authentication
                    UserController.php    # Handles user management
        Models/
            User.php                     # User model
    routes/
        api.php                         # API routes
    tests/
        Feature/
            ApiAuthTest.php             # Tests for authentication
            ApiUserTest.php             # Tests for user management
```

## Conclusion

This Laravel 12 API provides a solid foundation for building applications that require user authentication and management. By following RESTful principles and using Laravel Sanctum for authentication, it provides a secure and scalable backend for web and mobile applications.

For any questions or issues, please contact the project maintainer.
