#!/bin/bash

echo "🚀 Setting up NutriSport Laravel API..."

# Copy .env
if [ ! -f src/.env ]; then
    echo "📝 Creating .env file..."
    cp src/.env.example src/.env
fi

# Start containers
echo "🐳 Starting Docker containers..."
docker-compose up -d --build

# Wait for MySQL
echo "⏳ Waiting for MySQL..."
sleep 10

# Install dependencies
echo "📦 Installing Composer dependencies..."
docker-compose exec -T php composer install

# Generate keys
echo "🔑 Generating application key..."
docker-compose exec -T php php artisan key:generate

echo "🔑 Generating JWT secret..."
docker-compose exec -T php php artisan jwt:secret

# Run migrations
echo "🗄️ Running migrations..."
docker-compose exec -T php php artisan migrate --seed

# Storage link
echo "🔗 Creating storage link..."
docker-compose exec -T php php artisan storage:link

# Cache clear
echo "🧹 Clearing cache..."
docker-compose exec -T php php artisan cache:clear
docker-compose exec -T php php artisan config:clear

echo "✅ Setup complete!"
echo ""
echo "📍 API available at: http://localhost:8000/api"
echo "📧 Admin: admin@nutrisport.com / password"
echo "📧 Client: client@test.com / password"
