install:
	@docker run -it -w /data -v ${PWD}:/data:delegated -v ~/.composer:/root/.composer:delegated --entrypoint composer --rm registry.gitlab.com/grahamcampbell/php:7.4-base update
	@docker run -it -w /data -v ${PWD}:/data:delegated -v ~/.composer:/root/.composer:delegated --entrypoint composer --rm registry.gitlab.com/grahamcampbell/php:7.4-base bin phpunit update
	@docker run -it -w /data -v ${PWD}:/data:delegated -v ~/.composer:/root/.composer:delegated --entrypoint composer --rm registry.gitlab.com/grahamcampbell/php:7.4-base bin phpstan update

phpunit:
	@rm -f bootstrap/cache/*.php && docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/phpunit --rm registry.gitlab.com/grahamcampbell/php:7.4-cli

phpstan-analyze:
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/phpstan --rm registry.gitlab.com/grahamcampbell/php:7.4-cli analyze

phpstan-baseline:
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/phpstan --rm registry.gitlab.com/grahamcampbell/php:7.4-cli analyze --generate-baseline

test: phpunit phpstan-analyze

clean:
	@rm -rf composer.lock vendor vendor-bin/phpunit/composer.lock vendor-bin/phpunit/vendor vendor-bin/phpstan/composer.lock vendor-bin/phpstan/vendor
