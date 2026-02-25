# NutriSport Laravel REST API

A RESTful API built with Laravel to manage NutriSport e-commerce operations across multiple country sites (France, Italy, Belgium).

## Features

- Products management with site-specific pricing
- Customer authentication (JWT) and profile management
- Cart handling with Redis cache for guest and authenticated users
- Order processing with email notifications
- Admin endpoints to manage products and view recent orders
- Public product feeds (JSON/XML)
- Daily cron jobs for sales reporting and analytics

## Tech Stack

Laravel 12.x • MySQL 8.0 • Redis • JWT • Docker

## 🚀 Quick Setup

### Prerequisites
- Docker & Docker Compose
- Git

### Installation

```bash
# 1. Clone repository
git clone <repository-url>
cd laravel-api

# 2. Run setup script (Linux/Mac)
chmod +x setup.sh
./setup.sh

# OR Manual setup:

# Copy environment file
cp src/.env.example src/.env

# Start Docker containers
docker-compose up -d --build

# Install dependencies
docker-compose exec php composer install

# Generate keys
docker-compose exec php php artisan key:generate
docker-compose exec php php artisan jwt:secret

# Run migrations
docker-compose exec php php artisan migrate --seed

# Create storage link
docker-compose exec php php artisan storage:link
```

## 🌐 Access

- **API Base URL**: http://localhost:8000/api
- **MySQL**: localhost:3307 (user: laravel, pass: secret)
- **Redis**: localhost:6380

## 🔑 Default Credentials

**Admin**
- Email: admin@nutrisport.com
- Password: password

**Client**
- Email: client@test.com
- Password: password

## 📚 API Documentation

Import Postman collections from `postman/` directory:
- `1-Auth.postman_collection.json` - Authentication
- `2-Public.postman_collection.json` - Public endpoints
- `3-Client.postman_collection.json` - Client endpoints
- `4-Admin.postman_collection.json` - Admin endpoints
- `5-Vendeur.postman_collection.json` - Vendor endpoints

## 🧪 Testing

```bash
# Run all tests
docker-compose exec php php artisan test

# Run specific test
docker-compose exec php php artisan test --filter CartTest
```

## ⚙️ Useful Commands

```bash
# Clear cache
docker-compose exec php php artisan cache:clear

# Run daily report (cron)
docker-compose exec php php artisan report:daily

# Access PHP container
docker-compose exec php bash

# View logs
docker-compose logs -f php

# Stop containers
docker-compose down

# Rebuild containers
docker-compose up -d --build
```

## 📋 Key Endpoints

### Public
- `GET /api/public/products` - List products
- `GET /api/public/products/{id}` - Product details
- `GET /api/feeds/products/{format}` - Product feeds (json/xml)

### Auth
- `POST /api/auth/register` - Register
- `POST /api/auth/login` - Login
- `GET /api/auth/me` - Current user

### Cart
- `GET /api/cart` - View cart
- `POST /api/cart` - Add to cart
- `PUT /api/cart/{productId}` - Update quantity
- `DELETE /api/cart/{productId}` - Remove item

### Orders (Client)
- `GET /api/client/orders` - Order history
- `POST /api/client/orders` - Create order

### Admin
- `GET /api/admin/orders` - Recent orders (5 days)
- `POST /api/admin/products` - Create product

## 🔧 Configuration

### JWT Tokens
- Client: 6 hours (360 minutes)
- Agent: 8 hours (480 minutes)

### Cart Cache
- Duration: 3 days
- Storage: Redis

### Cron Schedule
- Daily report: Every day at 00:00

## 🐛 Troubleshooting

**Port conflicts**
```bash
# Change ports in docker-compose.yml if needed
# MySQL: 3307 (default)
# Redis: 6380 (default)
```

**Permission issues**
```bash
docker-compose exec php chown -R www-data:www-data /var/www/html/storage
docker-compose exec php chmod -R 775 /var/www/html/storage
```

**Database connection**
```bash
# Verify MySQL is running
docker-compose ps

# Check .env file
DB_HOST=mysql
DB_PORT=3306
```
