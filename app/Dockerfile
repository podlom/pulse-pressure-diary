FROM php:8.1-apache

# Оновлюємо пакетний менеджер і встановлюємо бібліотеку sqlite3 та інструмент pkg-config
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    pkg-config

# Встановлюємо PDO і розширення SQLite
RUN docker-php-ext-install pdo pdo_sqlite

# Копіюємо файли проекту в контейнер
COPY . /var/www/html/

# Створюємо директорію для бази даних і надаємо права
RUN mkdir -pv /var/www/html/data
RUN chown -Rv www-data:www-data /var/www/html/data
RUN chmod -Rv 755 /var/www/html/data

EXPOSE 80
