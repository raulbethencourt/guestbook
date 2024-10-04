SHELL := /bin/bash

tests:
	APP_ENV=test symfony console doctrine:database:drop --force || true
	APP_ENV=test symfony console doctrine:database:create
	APP_ENV=test symfony console doctrine:migrations:migrate -n 
	APP_ENV=test symfony console doctrine:fixtures:load -n 
	symfony php bin/phpunit --testdox $(MAKECMDGOALS)

.PHONY: tests
