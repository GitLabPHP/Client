install:
	@docker run -it -w /data -v ${PWD}:/data:delegated -v composer:/root/.composer:delegated --entrypoint=composer --rm registry.gitlab.com/grahamcampbell/php:7.4-base update
	@docker run -it -w /data -v ${PWD}:/data:delegated -v composer:/root/.composer:delegated --entrypoint composer --rm registry.gitlab.com/grahamcampbell/php:7.4-base bin all update

phpunit:
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/phpunit --rm registry.gitlab.com/grahamcampbell/php:7.4-cli

phpstan-analyze:
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/phpstan --rm registry.gitlab.com/grahamcampbell/php:7.4-cli analyze

phpstan-baseline:
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/phpstan --rm registry.gitlab.com/grahamcampbell/php:7.4-cli analyze --generate-baseline

psalm-analyze:
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/psalm.phar --rm registry.gitlab.com/grahamcampbell/php:7.4-cli

psalm-baseline:
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/psalm.phar --rm registry.gitlab.com/grahamcampbell/php:7.4-cli --set-baseline=psalm-baseline.xml

psalm-show-info:
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/psalm.phar --rm registry.gitlab.com/grahamcampbell/php:7.4-cli --show-info=true

rector-dry-run:
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/rector --rm registry.gitlab.com/grahamcampbell/php:7.4-cli process --dry-run

rector-fix:
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/rector --rm registry.gitlab.com/grahamcampbell/php:7.4-cli process

test: phpunit phpstan-analyze psalm-analyze rector-dry-run

clean:
	@rm -rf .phpunit.result.cache composer.lock vendor vendor-bin/*/composer.lock vendor-bin/*/vendor
