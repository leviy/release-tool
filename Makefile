sources = bin/release src/

.PHONY: all check static-analysis unit-tests integration-tests coding-standards

all: vendor

vendor: composer.json composer.lock
	composer install

check: static-analysis unit-tests integration-tests acceptance-tests system-tests coding-standards

static-analysis: vendor
	vendor/bin/parallel-lint $(sources)
	vendor/bin/phpstan analyse --level=7 --configuration=phpstan.neon $(sources)

unit-tests: vendor
	vendor/bin/phpunit --testsuite unit-tests

integration-tests: vendor
	vendor/bin/phpunit --testsuite integration-tests

acceptance-tests: vendor
	vendor/bin/behat

system-tests: vendor
	vendor/bin/phpunit --testsuite system-tests

coding-standards: vendor
	vendor/bin/phpcs -p --colors
	vendor/bin/phpmd src/ text phpmd.xml

bin/box.phar:
	curl -LS https://github.com/humbug/box/releases/download/3.0.0-beta.3/box.phar -o bin/box.phar
	chmod a+x bin/box.phar

dist: bin/box.phar
	mkdir -p build
	bin/box.phar compile

