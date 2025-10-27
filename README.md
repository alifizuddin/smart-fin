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

10. Log In with:

```bash
    admin@example.com, pw: password
    manager@example.com, pw: password
    user@example.com, pw: password
```

11. Download and Install tesseract OCR:

```bash
    https://github.com/UB-Mannheim/tesseract/wiki
```

12. Set Up a Python Virtual Environment
    In your project folder

```bash
    cd path\to\your\project
    python -m venv venv
```

13. Set Up a Python Virtual Environment
    In your project folder

```bash
    cd path\to\your\project
    python -m venv venv
    venv\Scripts\activate
```

14. Install All Required Python Packages

```bash
    pip install flask pytesseract pillow opencv-python-headless numpy google-generativeai
```

15. Run Flask API

```bash
    python app.py
```

## n8n Setup

This project uses n8n to handle automated workflows, which runs in its own Docker container.

1. **Start the n8n Container**
   In your project's root directory, run the following command. This will read the `docker-compose.n8n.yml` file and start the n8n service in the background.

    ```bash
    docker-compose -f docker-compose.n8n.yml up -d
    ```

    Alternatively, you can open your Docker Desktop application, find the **smart-fin** project, and press the "Start" button on the `n8n_local` container.

2. **Access n8n in Browser**
   Once the container is running, open your web browser and go to:
   `http://localhost:5678`
   _(This port is set in the `docker-compose.n8n.yml` file)_.

3. **Import the Project Workflow**
   You will see a blank n8n canvas. You must import the project's "blueprint":

    - Click **Import** (usually at the top of the screen).
    - Select **Import from File**.
    - Find and upload the `smartfin-workflow.json` file from the project directory.
