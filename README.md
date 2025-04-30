# Filament Equipment Management System

<p align="center">
<a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel Logo"></a>
&nbsp;&nbsp;&nbsp;
<a href="https://filamentphp.com/" target="_blank"><img src="https://github.com/filamentphp/filament/raw/3.x/art/banner.jpg" width="400" alt="Filament Logo"></a>
</p>

## About This Project

This is just a simple project to test the Filament admin panel.

## Features

- **User Management**: Create, update, and manage user accounts with role-based permissions
- **Role-based Access Control**: Granular control over user permissions using Filament Shield
- **Equipment Management**: Track equipment details, status, and other relevant information
- **Brand Management**: Organize equipment by brands
- **Modern Admin Interface**: Clean, responsive admin interface powered by Filament

## Tech Stack

- **Laravel 12**: The latest version of the PHP framework 
- **Filament v3**: Admin panel toolkit for Laravel
- **Filament Shield**: For role and permission management
- **Filament Notifications**: For in-app notifications

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- Database (MySQL, PostgreSQL, SQLite)

## Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd <repository-directory>
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Configure your database in the `.env` file

6. Run migrations:
   ```bash
   php artisan migrate
   ```

7. Seed the database (if applicable):
   ```bash
   php artisan db:seed
   ```

8. Install NPM dependencies:
   ```bash
   npm install
   ```

9. Compile assets:
   ```bash
   npm run build
   ```

10. Start the development server:
    ```bash
    php artisan serve
    ```

## Development

To run the project in development mode with hot reloading:

```bash
composer dev
```

This will start the Laravel server, queue worker, and Vite development server concurrently.

## Testing

```bash
composer test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
