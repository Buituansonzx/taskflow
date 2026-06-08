# TaskFlow - Docker Setup

## Services
| Service   | URL                        | Mô tả              |
|-----------|----------------------------|--------------------|
| App       | http://localhost           | Laravel app        |
| Mailpit   | http://localhost:8025      | Test email UI      |
| PostgreSQL| localhost:5432             | Database           |
| Redis     | localhost:6379             | Cache & Queue      |

## Cách chạy lần đầu

### 1. Tạo project Laravel
```bash
composer create-project laravel/laravel taskflow
cd taskflow
```

### 2. Copy các file Docker vào project
Copy toàn bộ file trong repo này vào thư mục project Laravel.

### 3. Build và khởi động
```bash
docker compose up -d --build
```

### 4. Setup Laravel
```bash
# Copy env
cp .env.example .env

# Vào container
docker compose exec php sh

# Trong container
composer install
php artisan key:generate
php artisan migrate
exit
```

### 5. Kiểm tra
- App: http://localhost
- Mail: http://localhost:8025

## Lệnh thường dùng

```bash
# Vào container PHP
docker compose exec php sh

# Chạy artisan
docker compose exec php php artisan <command>

# Xem queue logs
docker compose logs -f queue

# Restart queue worker
docker compose restart queue
```

## Cấu trúc Docker

```
docker/
├── nginx/
│   └── default.conf     # Nginx config
└── php/
    ├── Dockerfile        # PHP 8.3 + extensions
    └── local.ini         # PHP config
docker-compose.yml
.env.example
Makefile                  # Shortcuts
```
# taskflow
