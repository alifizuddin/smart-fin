# Project Name

## Prerequisites
- PHP >= 8.x
- Composer
- Node.js >= 14.x
- MySQL or other database
- Laravel 12

## Installation Guide
1. Clone the repository:
```bash
   git clone https://github.com/alifizuddin/smart-fin
   cd repo-name
```

2. Install PHP dependencies:
```bash
    composer install
```
3.  Install Node modules:
```bash
    npm install
```

4.  Set up environment variables:
```bash
    cp .env.example .env
```

5.  Generate application key:
```bash
    php artisan key:generate
```

6.  Run database migrations:
```bash
php artisan migrate
```

7.  Compile assets:
```bash
    npm run dev
```

8.  Start the application:
```bash
    php artisan serve OR php -S 127.0.0.1:8080 -t public
``` 

9.  Seed the database with admin account and the roles and permissions
```bash
    php artisan db:seed
```
10.  Log In with:
```bash
    admin@example.com, pw: password
    manager@example.com, pw: password
    user@example.com, pw: password
``` 
