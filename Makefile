install:
	composer install

validate:
	composer validate

lint:
	composer exec --verbose phpcs -- --standard=PSR12 bin src tests

test:
	composer run-script phpunit tests