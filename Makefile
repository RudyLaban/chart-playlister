# =============================================================================
# Chart Playlister — Makefile
# =============================================================================
# Interface de commandes unifiée pour le projet.
# Usage : make <commande>

# Détecte le binaire Docker Compose (v2)
DOCKER_COMPOSE = docker compose

# Exécuter une commande dans le container API
API_EXEC = $(DOCKER_COMPOSE) exec api
# Exécuter une commande dans le container Front
FRONT_EXEC = $(DOCKER_COMPOSE) exec front

# -----------------------------------------------------------------------------
# Commandes principales
# -----------------------------------------------------------------------------

.PHONY: install
install: build api-deps db-create db-migrate ## Installe le projet (build + deps + DB)
	@echo "✅ Projet installé avec succès"

.PHONY: dev
dev: ## Lance la stack de dev (docker compose up)
	$(DOCKER_COMPOSE) up -d
	@echo ""
	@echo "🚀 Chart Playlister lancé !"
	@echo "   API     → http://localhost:8080"
	@echo "   Front   → http://localhost:5173"
	@echo "   DB      → localhost:5432"
	@echo ""

.PHONY: stop
stop: ## Arrête la stack
	$(DOCKER_COMPOSE) down

.PHONY: destroy
destroy: ## Arrête + supprime les volumes (reset complet)
	$(DOCKER_COMPOSE) down -v
	@echo "💀 Stack détruite (volumes supprimés)"

.PHONY: logs
logs: ## Affiche les logs de tous les services
	$(DOCKER_COMPOSE) logs -f

.PHONY: logs-api
logs-api: ## Logs API uniquement
	$(DOCKER_COMPOSE) logs -f api

.PHONY: logs-front
logs-front: ## Logs front uniquement
	$(DOCKER_COMPOSE) logs -f front

# -----------------------------------------------------------------------------
# Docker
# -----------------------------------------------------------------------------

.PHONY: build
build: ## Build les images Docker
	$(DOCKER_COMPOSE) build

.PHONY: shell-api
shell-api: ## Shell bash dans le container API
	$(API_EXEC) bash

.PHONY: shell-front
shell-front: ## Shell dans le container Front
	$(FRONT_EXEC) sh

# -----------------------------------------------------------------------------
# API — Symfony
# -----------------------------------------------------------------------------

.PHONY: api-deps
api-deps: ## Installe les dépendances PHP (composer install)
	$(API_EXEC) composer install

.PHONY: db-create
db-create: ## Crée la base de données
	$(API_EXEC) php bin/console doctrine:database:create --if-not-exists

.PHONY: db-migrate
db-migrate: ## Exécute les migrations
	$(API_EXEC) php bin/console doctrine:migrations:migrate --no-interaction

.PHONY: db-fixtures
db-fixtures: ## Charge les fixtures
	$(API_EXEC) php bin/console doctrine:fixtures:load --no-interaction

.PHONY: db-reset
db-reset: ## Reset complet DB (drop + create + migrate + fixtures)
	$(API_EXEC) php bin/console doctrine:database:drop --force --if-exists
	$(API_EXEC) php bin/console doctrine:database:create
	$(API_EXEC) php bin/console doctrine:migrations:migrate --no-interaction
	$(API_EXEC) php bin/console doctrine:fixtures:load --no-interaction
	@echo "✅ Base de données réinitialisée"

# -----------------------------------------------------------------------------
# Front — Vue
# -----------------------------------------------------------------------------

.PHONY: front-deps
front-deps: ## Installe les dépendances Node (npm install)
	$(FRONT_EXEC) npm install

# -----------------------------------------------------------------------------
# Qualité de code
# -----------------------------------------------------------------------------

.PHONY: lint
lint: lint-api lint-front ## Lance tous les linters

.PHONY: lint-api
lint-api: ## Lint PHP (PHP-CS-Fixer)
	$(API_EXEC) vendor/bin/php-cs-fixer fix --dry-run --diff

.PHONY: lint-front
lint-front: ## Lint Front (ESLint)
	$(FRONT_EXEC) npx eslint src/

.PHONY: stan
stan: ## Analyse statique PHP (PHPStan)
	$(API_EXEC) vendor/bin/phpstan analyse

# -----------------------------------------------------------------------------
# Tests
# -----------------------------------------------------------------------------

.PHONY: test
test: test-api test-front ## Lance tous les tests

.PHONY: test-api
test-api: ## Tests API (PHPUnit)
	$(API_EXEC) php bin/phpunit

.PHONY: test-front
test-front: ## Tests Front (Vitest)
	$(FRONT_EXEC) npx vitest run

# -----------------------------------------------------------------------------
# Scraping
# -----------------------------------------------------------------------------

.PHONY: scrape
scrape: ## Lance le scraping de tous les providers actifs
	$(API_EXEC) php bin/console app:scrape-charts

# -----------------------------------------------------------------------------
# Aide
# -----------------------------------------------------------------------------

.PHONY: help
help: ## Affiche cette aide
	@echo ""
	@echo "Commandes disponibles (make <commande>) :"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*## ' Makefile | \
		awk -F ':.*## ' '{printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'
	@echo ""

.DEFAULT_GOAL := help
