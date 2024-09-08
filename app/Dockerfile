FROM php:8.1-apache

# Оновлюємо пакетний менеджер і встановлюємо бібліотеки для SQLite та MySQL
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    pkg-config \
    libonig-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql

# Копіюємо файли проекту в контейнер
COPY . /var/www/html/

# Створюємо директорію для бази даних і надаємо права
RUN mkdir -pv /var/www/html/data
RUN chown -Rv www-data:www-data /var/www/html/data
RUN chmod -Rv 755 /var/www/html/data

# Встановлюємо права на папку для завантаження
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 80
