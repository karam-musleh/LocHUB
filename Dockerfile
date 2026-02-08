FROM php:8.4-fpm

# تثبيت الامتدادات المطلوبة للـLaravel
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install zip pdo_mysql

# نسخ المشروع
WORKDIR /var/www
COPY . .

# تثبيت Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# تثبيت الحزم
RUN composer install --optimize-autoloader --no-interaction

# تشغيل السيرفر
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
