# Roodito API Documentation

## Authentication Endpoints

### Login

**POST** `/api/login`

Login with email or phone number.

**Request Body:**

```json
{
    "identifier": "user@example.com", // or phone number like "254712345678"
    "password": "your_password"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John",
            "email": "user@example.com",
            "phone_number": "254712345678"
            // ... other user fields
        },
        "api_token": "generated_token_here"
    }
}
```

### Logout

**POST** `/api/logout`

Logout and invalidate the current API token.

**Headers:**

```
Authorization: Bearer your_api_token_here
```

**Response:**

```json
{
    "success": true,
    "message": "Logout successful"
}
```

### Get Current User

**GET** `/api/me`

Get the current authenticated user's information.

**Headers:**

```
Authorization: Bearer your_api_token_here
```

**Response:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John",
        "email": "user@example.com"
        // ... other user fields
    }
}
```

## User Search Endpoints (Public)

### Search Users

**GET** `/api/search/users`

Search for users by partial email or phone number.

**Query Parameters:**

```
query=search_term (minimum 2 characters)
```

**Response:**

```json
{
    "success": true,
    "message": "Users found.",
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone_number": "254712345678",
            "username": "johndoe"
        }
    ]
}
```

### Check User Exists

**POST** `/api/check/user-exists`

Check if a specific email or phone number exists.

**Request Body:**

```json
{
    "identifier": "user@example.com" // or phone number
}
```

**Response (User Exists):**

```json
{
    "success": true,
    "message": "User exists.",
    "exists": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "phone_number": "254712345678",
        "username": "johndoe"
    }
}
```

**Response (User Doesn't Exist):**

```json
{
    "success": false,
    "message": "User does not exist.",
    "exists": false
}
```

## User Management Endpoints

### Get All Users (Admin)

**GET** `/api/users`

Get all users (requires authentication).

**Headers:**

```
Authorization: Bearer your_api_token_here
```

### Get User by ID

**GET** `/api/users/{id}`

Get a specific user by ID.

### Create User

**POST** `/api/users`

Create a new user.

### Update User

**PUT** `/api/users/{id}`

Update a user.

### Delete User

**DELETE** `/api/users/{id}`

Delete a user.

## Usage Examples

### Login with Email

```bash
curl -X POST http://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "identifier": "user@example.com",
    "password": "password123"
  }'
```

### Login with Phone Number

```bash
curl -X POST http://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "identifier": "254712345678",
    "password": "password123"
  }'
```

### Get Current User

```bash
curl -X GET http://your-domain.com/api/me \
  -H "Authorization: Bearer your_api_token_here"
```

### Search Users

```bash
curl -X GET "http://your-domain.com/api/search/users?query=john"
```

### Check if User Exists

```bash
curl -X POST http://your-domain.com/api/check/user-exists \
  -H "Content-Type: application/json" \
  -d '{
    "identifier": "user@example.com"
  }'
```

## Notes

-   The API token is automatically generated on successful login
-   Passwords are hashed using SHA1 (as per existing codebase)
-   Phone numbers should be in Kenyan format (254XXXXXXXXX)
-   All protected routes require the `Authorization: Bearer token` header
-   The `is_logged_in` field tracks user login status
-   `last_login` and `last_logout` timestamps are automatically updated
