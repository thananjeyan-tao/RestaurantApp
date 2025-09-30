FROM ubuntu:24.04

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive

# Install PHP, extensions, composer
RUN apt-get update && apt-get install -y \
    curl zip unzip git sqlite3 supervisor \
    php8.4-cli php8.4-mysql php8.4-redis php8.4-mbstring php8.4-xml php8.4-curl php8.4-gd php8.4-bcmath \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy app files
COPY . .

# Ensure storage + bootstrap/cache writable
RUN chmod -R 777 storage bootstrap/cache

# Run Laravel dev server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]

EXPOSE 80
