# ==========================================
# Stage 1: Build Frontend Assets (Vite)
# ==========================================
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

# ==========================================
# Stage 2: Production PHP + Nginx Environment
# ==========================================
FROM richarvey/nginx-php-fpm:latest

# Install PostgreSQL headers, image libraries, and compile required PHP extensions
RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_pgsql pgsql

WORKDIR /var/www/html

# Copy application source code
COPY . .

# Copy compiled frontend assets from Vite build stage
COPY --from=frontend /app/public/build /var/www/html/public/build

# Environment flags for richarvey/nginx-php-fpm image
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1
ENV PHP_CATCHALL 1

# Laravel Production Defaults
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
ENV COMPOSER_ALLOW_SUPERUSER 1

# Install PHP dependencies for production
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# Set correct permissions and ensure deployment script is executable
RUN chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod +x /var/www/html/scripts/00-laravel-deploy.sh

CMD ["/start.sh"]
