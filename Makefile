install:
	composer install

validate:
	composer validate

lint:
	composer run-script phpcs -- --standard=PSR12 bin src tests -np

test:
	composer run-script phpunit tests

test-coverage:
	composer run-script phpunit tests -- --coverage-clover build/logs/clover.xml
