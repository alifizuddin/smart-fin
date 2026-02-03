# SmartFin360

## 📌 Prerequisites

-   Docker & Docker Compose
-   Ollama (for local AI model hosting)
-   Ngrok (for exposing webhooks to the internet)
-   Node.js & NPM

## ⚙️ Environment Configuration

1. Clone the repository:

```bash
   git clone https://github.com/alifizuddin/smart-fin
   cd repo-name
```

2.  Set up environment variables:

```bash
    cp .env.example .env
```

3.  Update Database Credentials:

```bash
DB_HOST=db
DB_DATABASE=smartfin
DB_USERNAME=smartfin_user
DB_PASSWORD=12345
```

## 🐳 Docker Deployment

SmartFin uses these containers:
- Laravel app Container
- n8n Automation
- Nginx web server
- mySQL database
- Background Queue Worker

1. Build and start services:

```bash
   docker-compose up -d --build
```

2. Verify Running Containers:

```bash
   docker ps
```

You should see:
- smartfin-app ✅
- smartfin-queue ✅
- n8n_local ✅
- smartfin-web ✅
- smartfin-db ✅

## 🚀 Application Initialization

Run the following commands inside the smartfin-app container.

1. Install PHP services:

```bash
   docker exec -it smartfin-app composer install
```

2. Generate Laravel Application Key:

```bash
   docker exec -it smartfin-app php artisan key:generate
```

3. Run Database Migration & Seeder:

```bash
   docker exec -it smartfin-app php artisan migrate --seed
```

4. Compile Frontend Assets:

```bash
   npm install
   npm run build
```

## 🤖 Local AI Setup (Ollama)

1. Pull Vision Model:

```bash
   ollama pull qwen3-vl:8b
```

2. Pull Language Model:

```bash
   ollama pull gpt-oss:20b
```

## 🔄 n8n Automation Setup

1. Access n8n dashboard:

```bash
   http://localhost:5678
```

2. Import workflows:

- Navigate to Workflows -> Import
- Upload the following files into the workflows:

```bash
   - SmartFin Receipt Processor.json
   - SmartFin Anomaly Detector.json
   - SmartFin AISuggestor.json
   - SmartFin Expense Forecasting.json
   - SmartFin Chatbot.json
```

## 🌐 Ngrok Setup (External Webhooks)

1. Expose n8n Port:

```bash
   ngrok http 5678
```

## 🖥 Accessing the System

1. Main Application:

```bash
   http://localhost:8000
```

2. n8n Dashboard:

```bash
   http://localhost:5678
```

## 🔑 Default Test Accounts

```bash
   admin@axample.com, pw: password
   financeadmin@example.com, pw: password
   manager@example.com, pw: password
   staff@example.com, pw: password
```






