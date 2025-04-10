# Testing Guide

## Overview

The project includes comprehensive test coverage using PHPUnit. Tests are organized in the `tests/Feature` directory for feature tests and `tests/Unit` for unit tests.

## Running Tests

### All Tests
```bash
php artisan test
```

### Specific Test Files
```bash
php artisan test --filter=ApiAuthTest
php artisan test --filter=ApiUserTest
```

### Specific Test Methods
```bash
php artisan test --filter=test_user_can_register_api
```

## Test Structure

### Authentication Tests
- Registration validation
- Login with valid credentials
- Login with invalid credentials
- Token authentication
- Logout functionality

### User Management Tests
- Create user
- Update user
- Delete user
- List users
- View single user

## Writing Tests

1. Create a new test file:
```bash
php artisan make:test YourTestName
```

2. Use Laravel's testing helpers:
```php
$response = $this->postJson('/api/endpoint', $data);
$response->assertStatus(200);
```

3. Use factories for test data:
```php
$user = User::factory()->create();
```

## Test Environment

Tests use an SQLite database in memory for faster execution. This is configured in `phpunit.xml`.
