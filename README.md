# Щоденник показників вимірювання тиску та пульсу

Щоб почати виконайте такі команди:

```bash
cd app
```

## Конфігурація

```bash
cp -v .env_example.txt .env
cp -v config_example.php config.php
```

Оберіть тип бази даних опція DB_DRIVER може мати значення sqlite (за замовчуванням).
В такому випадку треба відповідним чином налаштувати параметр path в масиві файлу config.php

```php
$config = [
    'db' => [
        'driver' => $driver,
        'sqlite' => [
            'path' => __DIR__ . '/data/pressure_pulse_log.db',
        ],
```

Якщо ви хочете використовувати MySQL, тоді створіть базу даних, користувача, а також налаштуйте параметри:

```dotenv
DB_HOST=yourDbName
DB_NAME=pressure_pulse_log
DB_USER=your_db_user
DB_PASSWORD=your_mysql_password
```

Для запуску проекту в Docker середовищі переконайтесь, що значення середовища бази даних з файлу app/docker-compose.yml відповідають вашим налаштуванням у файлі app/.env

```yaml
      - db
    environment:
      DB_DRIVER: ${DB_DRIVER:-sqlite} # За замовчуванням sqlite
      DB_HOST: yourDbName
      DB_NAME: pressure_pulse_log
      DB_USER: your_db_user
      DB_PASSWORD: your_mysql_password
```

## Білд та запуск у Docker

Запустіть команду:

```bash
docker-compose build --no-cache
```

для побудови Docker середовища.

І команду:

```bash
docker-compose up -d
```
для запуску додатка в середовищі Docker.

Коли процес запуску Docker успішно закінчиться відкрийте посилання: <http://localhost:8080> в браузері.

Щоб зупинити роботу Docker виконайте команду:

```bash
docker-compose down
```
