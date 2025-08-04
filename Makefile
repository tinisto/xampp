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

check: lint ## Run all checks before commit