# Lootora — Docker convenience targets
.PHONY: up down build rebuild logs shell mysql ps migrate seed fresh optimize clear test prod-up prod-down push

COMPOSE      ?= docker compose
COMPOSE_PROD ?= docker compose -f docker-compose.prod.yml

up: ## Build (if needed) and start the dev stack in the background
	$(COMPOSE) up -d --build

down: ## Stop and remove containers (keeps volumes)
	$(COMPOSE) down

build: ## Build the app image without starting
	$(COMPOSE) build

rebuild: ## Force rebuild with no cache
	$(COMPOSE) build --no-cache

logs: ## Tail the app container logs
	$(COMPOSE) logs -f app

ps: ## Show running services
	$(COMPOSE) ps

shell: ## Open a shell in the app container
	$(COMPOSE) exec app bash

mysql: ## Open a MySQL shell
	$(COMPOSE) exec mysql mysql -u$${DB_USERNAME:-lootora} -p$${DB_PASSWORD:-lootora_password} $${DB_DATABASE:-lootora}

migrate: ## Run database migrations
	$(COMPOSE) exec app php artisan migrate --force

seed: ## Run database seeders (idempotent)
	$(COMPOSE) exec app php artisan db:seed --force

fresh: ## DANGER — drop all tables, re-migrate and re-seed
	$(COMPOSE) exec app php artisan migrate:fresh --force --seed

optimize: ## Cache config / route / view for production speed
	$(COMPOSE) exec app php artisan config:cache
	$(COMPOSE) exec app php artisan route:cache
	$(COMPOSE) exec app php artisan view:cache

clear: ## Clear all Laravel caches
	$(COMPOSE) exec app php artisan optimize:clear

test: ## Run phpunit
	$(COMPOSE) exec app php artisan test

# ------- Production helpers -------
prod-up:
	$(COMPOSE_PROD) up -d

prod-down:
	$(COMPOSE_PROD) down

push: ## Push the built image to Docker Hub (requires DOCKER_IMAGE env)
	@test -n "$$DOCKER_IMAGE" || (echo "Set DOCKER_IMAGE=registry/your-user/lootora:tag"; exit 1)
	docker tag lootora/app:latest $$DOCKER_IMAGE
	docker push $$DOCKER_IMAGE
