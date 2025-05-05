.PHONY: help ps fresh build start stop destroy migrate \
	migrate-fresh create-env

CONTAINER_NGINX=nginx
CONTAINER_PHP-FPM=php-fpm
CONTAINER_PHP-CLI=php-cli
CONTAINER_MYSQL=mysql
VOLUME_DATABASE=what-to-watch-php_database

help: ## Print help.
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n\nTargets:\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2 }' $(MAKEFILE_LIST)

ps: ## Show containers.
	@docker compose ps

build: create-env ## Build all containers.
	@docker compose build --no-cache

start: create-env ## Start all containers.
	@docker compose up --force-recreate -d

stop: ## Destroy all containers without deleting volumes.
	@docker compose down

fresh: stop destroy build start ## Destroy & recreate all containers.

destroy: ## Destroy all containers and volumes.
	@docker compose down
	@if [ "$(shell docker volume ls --filter name=${VOLUME_DATABASE} --format {{.Name}})" ]; then \
		docker volume rm ${VOLUME_DATABASE}; \
	fi

migrate: ## Run migration files.
	@docker compose exec ${CONTAINER_PHP-CLI} php artisan migrate

migrate-fresh: ## Clear database and run all migrations.
	@docker compose exec ${CONTAINER_PHP-CLI} php artisan migrate:fresh

seed: ## Run migration files.
	@docker compose exec ${CONTAINER_PHP-CLI} php artisan db:seed --class=DatabaseSeeder

create-env: ## Copy .env.example to .env
	@if [ ! -f ".env" ]; then \
		echo "Creating .env file."; \
		cp .env.example .env; \
	fi
