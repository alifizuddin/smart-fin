# Smart Finance 360

## Prerequisites

-   PHP >= 8.x
-   Composer
-   Node.js >= 14.x
-   MySQL or other database
-   Laravel 12
-   Python 3.8+
-   Pip (Python package manager)
-   Docker Desktop (Requires WSL 2 setup on Windows)

## Installation Guide

1. Clone the repository:

```bash
   git clone https://github.com/alifizuddin/smart-fin
   cd repo-name
```
1.  Set up environment variables:

```bash
    cp .env.example .env
```

2. Build and Start All Docker Services:

```bash
    docker-compose up -d --build
```

3.  Install PHP Dependencies:

```bash
    docker-compose exec app composer install
```

4.  Install JS dependencies and build assets:

```bash
    docker-compose exec app npm install
    docker-compose exec app npm run build
```

5.  Generate application key:

```bash
    docker-compose exec app php artisan key:generate
```

6.  Run database migrations:

```bash
    docker-compose exec app php artisan migrate
```

7.  Seed the database (with admin/user accounts)

```bash
    docker-compose exec app php artisan db:seed
```

8. Log In with:

```bash
    admin@example.com, pw: password
    manager@example.com, pw: password
    user@example.com, pw: password
```
