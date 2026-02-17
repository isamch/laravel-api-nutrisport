# NutriSport Laravel REST API

A RESTful API built with Laravel to manage NutriSport e-commerce operations across multiple country sites (France, Italy, Belgium).

## Features

- Products management with site-specific pricing
- Customer authentication (JWT) and profile management
- Cart handling with Redis cache for guest and authenticated users
- Order processing with email notifications and real-time updates (Pusher)
- Admin endpoints to manage products and view recent orders
- Public product feeds (JSON/XML)
- Daily cron jobs for sales reporting and analytics

## Tech Stack

Laravel 12.x • MySQL 8.0 • Redis • JWT • Pusher • Docker

## Setup

```bash
# 1. Clone and navigate
git clone <repository-url>
cd laravel-api

# 2. Configure environment
cp src/.env.example src/.env
# Edit src/.env with your database and service credentials

# 3. Start Docker containers
docker-compose up -d --build

# 4. Install dependencies and setup
docker-compose exec php composer install
docker-compose exec php php artisan key:generate
docker-compose exec php php artisan migrate
docker-compose exec php php artisan jwt:secret
docker-compose exec php php artisan storage:link
```

Access the API at: **http://localhost:8000/api**
