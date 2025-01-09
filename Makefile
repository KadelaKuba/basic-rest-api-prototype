help:
	@ echo "Usage: make <target>\n"
	@ echo "Available targets:\n"
	@ cat Makefile | grep -oE "^[^: ]+:" | grep -oE "[^:]+" | grep -Ev "help|default|.PHONY"

run:
	docker-compose up -d

stop:
	docker-compose down

container-bash:
	docker-compose exec task-api-php-fpm bash

build-dev:
	docker-compose exec task-api-php-fpm composer build-dev

standards:
	docker-compose exec task-api-php-fpm composer standards

standards-fix:
	docker-compose exec task-api-php-fpm composer standards-fix

phpstan:
	docker-compose exec task-api-php-fpm composer phpstan

check-all:
	docker-compose exec task-api-php-fpm composer check-all

migrations-diff:
	docker-compose exec task-api-php-fpm composer migrations-diff

migrations-migrate:
	docker-compose exec task-api-php-fpm composer migrations-migrate

generate-open-api-schema:
	docker-compose exec task-api-php-fpm composer generate-open-api-schema
