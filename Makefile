.PHONY: clean up test
clean: ## Clean up containers
	docker-compose down -v

up: ## Up the containers
	docker-compose build
	docker-compose up -d

test: up ## Test API
	docker-compose exec php composer install
	docker-compose exec php /app/vendor/phpunit/phpunit/phpunit --no-configuration /app/test