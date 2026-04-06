# Docker Setup Guide

## Prerequisites

- Docker Desktop installed
- Docker Compose (included with Docker Desktop)
- Git

---

## Development Environment

### 1. Initial Setup

```bash
# Clone the repository (if not already cloned)
git clone <your-repo-url>
cd fursgo

# Copy environment file
copy .env.example .env

# Install PHP dependencies locally (optional, for IDE support)
composer install

# Install Node dependencies (for asset building)
npm install
```

### 2. Environment Configuration

Edit `.env` file:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=host.docker.internal
DB_PORT=3306
DB_DATABASE=fursgo_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Build and Start Containers

```bash
# Build and start all services
docker-compose up -d --build

# View logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f app
docker-compose logs -f mysql
```

### 4. Database Setup

```bash
# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate

# Run seeders (optional)
docker-compose exec app php artisan db:seed

# Fresh start with seeders
docker-compose exec app php artisan migrate:fresh --seed
```

### 5. Asset Building

```bash
# Build assets for development
docker-compose exec app npm run dev

# Or build for production (minified)
docker-compose exec app npm run build
```

### 6. Development Workflow Commands

```bash
# Access application container shell
docker-compose exec app bash

# Run artisan commands
docker-compose exec app php artisan <command>

# Run composer commands
docker-compose exec app composer <command>

# Run npm commands
docker-compose exec app npm <command>

# Stop containers
docker-compose stop

# Stop and remove containers
docker-compose down

# Stop and remove containers with volumes (WARNING: deletes database)
docker-compose down -v
```

### 7. Access Points

- **Application**: http://localhost:8080

---

## Production Environment

### 1. Server Preparation

```bash
# Install Docker on Ubuntu/Debian
sudo apt-get update
sudo apt-get install -y docker.io docker-compose-plugin

# Start Docker
sudo systemctl start docker
sudo systemctl enable docker

# Add user to docker group (optional)
sudo usermod -aG docker $USER
```

### 2. Production Environment File

Create `.env.production`:

```env
APP_NAME=Fursgo
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your-production-mysql-host
DB_PORT=3306
DB_DATABASE=fursgo_prod
DB_USERNAME=your_prod_user
DB_PASSWORD=your_prod_password

# Security
APP_KEY=<generate-with-php-artisan-key-generate>
```

### 3. Production Docker Compose

Production uses `docker-compose.prod.yml` (only Apache/PHP container, external MySQL):

```yaml
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
      args:
        - APP_ENV=production
    container_name: fursgo-app-prod
    ports:
      - "80:80"
    environment:
      - APACHE_DOCUMENT_ROOT=/var/www/html/public
    networks:
      - fursgo-prod-network
    restart: unless-stopped
    # No volume mounts in production (immutable container)

networks:
  fursgo-prod-network:
    driver: bridge
```

### 4. Production Deployment Steps

```bash
# 1. Copy environment file
cp .env.production .env

# 2. Build and deploy
docker-compose -f docker-compose.prod.yml up -d --build

# 3. Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# 4. Cache configuration
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
```

### 5. Production Best Practices

#### Security
- Use strong passwords
- Keep `.env` file secure and never commit it
- Use Docker secrets or environment variable injection
- Enable HTTPS with reverse proxy (nginx/traefik)
- Regular security updates

#### Performance
```bash
# Optimize Laravel
docker-compose exec app php artisan optimize
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Enable OPcache in production Dockerfile
```

#### Monitoring
```bash
# Check container status
docker-compose ps

# View resource usage
docker stats

# Check logs
docker-compose logs -f --tail=100
```

#### Backups

Backups are handled by your external MySQL server.

---

## Common Commands Reference

### Development

| Command | Description |
|---------|-------------|
| `docker-compose up -d --build` | Build and start |
| `docker-compose down` | Stop and remove |
| `docker-compose down -v` | Stop, remove, and delete volumes |
| `docker-compose exec app bash` | Enter container shell |
| `docker-compose exec app php artisan migrate` | Run migrations |
| `docker-compose logs -f` | Follow logs |

### Production

| Command | Description |
|---------|-------------|
| `docker-compose -f docker-compose.prod.yml up -d --build` | Deploy |
| `docker-compose -f docker-compose.prod.yml exec app php artisan optimize` | Optimize |
| `docker system prune -a` | Clean unused images/containers |

---

## Troubleshooting

### Port Already in Use
```bash
# Find process using port
netstat -ano | findstr :8080

# Change port in .env
APP_PORT=8082
```

### Permission Issues
```bash
# Fix storage permissions
docker-compose exec app chmod -R 777 storage bootstrap/cache
```

### Database Connection Issues
```bash
# Check if MySQL is accessible from container
docker-compose exec app php -r "var_dump(mysqli_connect('host.docker.internal', 'root', 'password'));"

# Restart services
docker-compose restart
```

### Rebuild from Scratch
```bash
docker-compose down -v
docker-compose up -d --build
```
