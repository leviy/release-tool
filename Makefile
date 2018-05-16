.PHONY: all check static-analysis unit-tests coding-standards

all: vendor

vendor: composer.json $(wildcard composer.lock)
	composer install

check: static-analysis unit-tests coding-standards

static-analysis: vendor
	vendor/bin/parallel-lint src/
	vendor/bin/phpstan analyse --level=7 src/

unit-tests: vendor
	vendor/bin/phpunit --testsuite unit-tests

coding-standards: vendor
	vendor/bin/phpcs -p --colors
	vendor/bin/phpmd src/ text phpmd.xml
