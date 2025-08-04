.PHONY: help stan cs cs-fix lint fix check

help: ## Show this help message
	@echo "Available commands:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

stan: ## Run PHPStan analysis
	./vendor/bin/phpstan analyse

cs: ## Check code style with PHP_CodeSniffer
	./vendor/bin/phpcs

cs-fix: ## Fix code style with PHP CS Fixer
	./vendor/bin/php-cs-fixer fix --dry-run --diff

fix: ## Apply code style fixes
	./vendor/bin/php-cs-fixer fix

lint: stan cs ## Run all linters (PHPStan + PHPCS)

test: ## Run all tests
	./vendor/bin/phpunit

test-unit: ## Run unit tests only
	./vendor/bin/phpunit tests/Unit

test-integration: ## Run integration tests only
	./vendor/bin/phpunit tests/Integration

test-coverage: ## Run tests with coverage report
	./vendor/bin/phpunit --coverage-html tests/coverage/html

migrate: ## Run database migrations
	php database/migrate.php migrate

migrate-status: ## Show migration status
	php database/migrate.php status

migrate-rollback: ## Rollback last migration
	php database/migrate.php rollback

migrate-create: ## Create new migration (usage: make migrate-create name=migration_name)
	php database/migrate.php create $(name)

minify: ## Minify CSS and JavaScript assets
	php build/minify-assets.php

check: lint test ## Run all checks and tests before commit