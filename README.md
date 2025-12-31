# RP - Role & Permissions Management Package

A comprehensive Laravel package for managing users, roles, and permissions using Spatie Laravel Permissions.

## Features

- **User Management**: Full CRUD operations for users with multiple role assignment
- **Role Management**: Create, edit, and manage roles with permission assignment
- **Permission Management**: Create and manage permissions
- **Settings**: Configurable settings page for package configuration
- **Bootstrap 5 UI**: Beautiful, responsive interface matching your application's design
- **Search & Pagination**: Built-in search and pagination for all listing pages

## Installation

### As a Path Repository (Local Development)

1. Add the package to your `composer.json`:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "./packages/rp"
    }
  ],
  "require": {
    "nuxtit/rp": "*"
  }
}
```

2. Install the package:

```bash
composer require nuxtit/rp:@dev
```

3. The package will be auto-discovered by Laravel.

## Configuration

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag=rp-config
```

This will create `config/rp.php` where you can customize:

- `route_prefix`: Change the route prefix (default: `rp`)
- `user_model`: Specify your user model class
- `middleware`: Customize middleware for routes
- `items_per_page`: Number of items per page in listings

## Usage

### Routes

All routes are prefixed with `/rp` by default and require authentication and admin role:

- `/rp/users` - User management
- `/rp/roles` - Role management
- `/rp/permissions` - Permission management
- `/rp/settings` - Settings page

### User Management

- Create users with multiple role assignments
- Edit user details and roles
- View user details including roles and permissions
- Delete users (with protection against self-deletion)

### Role Management

- Create roles with permission assignments
- Edit roles and their permissions
- View role details
- Delete roles

### Permission Management

- Create permissions
- Edit permission names
- View permission details and associated roles
- Delete permissions

## Requirements

- PHP ^8.2
- Laravel ^12.0
- Spatie Laravel Permissions ^6.16

## Menu Integration

The package includes a menu item that can be added to your admin sidebar. The menu structure is already included in the package views.

## Security

- All routes are protected by `auth` middleware
- All routes require `admin` role
- User deletion prevents self-deletion
- Form validation on all inputs

## License

MIT
