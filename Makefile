# ─────────────────────────────────────────────
# TaskFlow - Makefile shortcuts
# ─────────────────────────────────────────────

# Khởi động toàn bộ
up:
	docker compose up -d

# Dừng toàn bộ
down:
	docker compose down

# Build lại images
build:
	docker compose up -d --build

# Xem logs
logs:
	docker compose logs -f

logs-php:
	docker compose logs -f php

logs-queue:
	docker compose logs -f queue

# Vào container PHP
bash:
	docker compose exec php sh

# ─── Laravel commands ───────────────────────
install:
	docker compose exec php composer install
	docker compose exec php cp .env.example .env
	docker compose exec php php artisan key:generate
	docker compose exec php php artisan migrate --seed

migrate:
	docker compose exec php php artisan migrate

migrate-fresh:
	docker compose exec php php artisan migrate:fresh --seed

seed:
	docker compose exec php php artisan db:seed

tinker:
	docker compose exec php php artisan tinker

test:
	docker compose exec php php artisan test

# ─── Permissions ────────────────────────────
permissions:
	docker compose exec php chmod -R 775 storage bootstrap/cache
	docker compose exec php chown -R www:www storage bootstrap/cache
