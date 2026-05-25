FROM laravelsail/php83-composer:latest

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    nodejs \
    npm \
    netcat-openbsd \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm install \
    && npm run build \
    && npm install -g vite

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD ["sh", "-c", "\
  echo 'Menunggu koneksi ke MySQL...' && \
  while ! nc -z \"$DB_HOST\" \"$DB_PORT\"; do \
    echo 'MySQL belum siap...' && sleep 5; \
  done && \
  php artisan migrate --seed --force && \
  php artisan config:cache && \
  php artisan route:cache && \
  php artisan view:cache && \
  php artisan storage:link && \
  echo '🚀 Memulai supervisord...' && \
  /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf \
"]
