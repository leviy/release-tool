sources = bin/release src/

.PHONY: all check static-analysis unit-tests integration-tests coding-standards

all: vendor

vendor: composer.json $(wildcard composer.lock)
	composer install

check: static-analysis unit-tests integration-tests coding-standards

static-analysis: vendor
	vendor/bin/parallel-lint $(sources)
	vendor/bin/phpstan analyse --level=7 $(sources)

unit-tests: vendor
	vendor/bin/phpunit --testsuite unit-tests

integration-tests: vendor
	vendor/bin/phpunit --testsuite integration-tests

coding-standards: vendor
	vendor/bin/phpcs -p --colors
	vendor/bin/phpmd src/ text phpmd.xml
