# API Documentation

## Authentication Endpoints

| Method | Endpoint       | Description              | Auth Required |
|--------|---------------|--------------------------|--------------|
| POST   | /api/register | Register a new user      | No           |
| POST   | /api/login    | Login and get token      | No           |
| POST   | /api/logout   | Invalidate token         | Yes          |
| GET    | /api/user     | Get authenticated user   | Yes          |

### Example Requests

#### Registration
```json
POST /api/register
{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

#### Login
```json
POST /api/login
{
    "email": "test@example.com",
    "password": "password"
}
```

Authentication uses Bearer tokens:
```
Authorization: Bearer <your_token>
```

## User Management Endpoints

| Method | Endpoint         | Description         | Auth Required |
|--------|-----------------|---------------------|--------------|
| GET    | /api/users      | Get all users       | Yes          |
| GET    | /api/users/{id} | Get user by ID      | Yes          |
| POST   | /api/users      | Create new user     | Yes          |
| PUT    | /api/users/{id} | Update user by ID   | Yes          |
| DELETE | /api/users/{id} | Delete user by ID   | Yes          |

### Example Requests

#### Create User
```json
POST /api/users
{
    "name": "New User",
    "email": "new.user@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

#### Update User
```json
PUT /api/users/1
{
    "name": "Updated Name",
    "email": "updated.email@example.com"
}
```

## Response Codes

| Status Code | Description                 |
|-------------|-----------------------------|
| 200         | Success                     |
| 201         | Resource created            |
| 401         | Unauthorized                |
| 422         | Validation error            |
| 500         | Server error                |
