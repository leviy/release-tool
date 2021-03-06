sources = bin/release config/ src/

.PHONY: all dist check test static-analysis unit-tests integration-tests coding-standards security-tests

all: vendor

vendor: composer.json composer.lock
	composer install

build/release-tool.phar: $(sources) bin/box.phar composer.lock vendor
	mkdir -p build
	bin/box.phar compile

dist: build/release-tool.phar

check test: static-analysis unit-tests integration-tests acceptance-tests system-tests coding-standards security-tests

static-analysis: vendor
	vendor/bin/phpstan analyse

unit-tests: vendor
	vendor/bin/phpunit --testsuite unit-tests

integration-tests: vendor
	vendor/bin/phpunit --testsuite integration-tests

acceptance-tests: vendor
	vendor/bin/behat

system-tests: vendor build/release-tool.phar
	vendor/bin/phpunit --testsuite system-tests

coding-standards: vendor
	vendor/bin/phpcs -p --colors
	vendor/bin/phpmd src/ text phpmd.xml

security-tests: vendor
	vendor/bin/security-checker security:check

bin/box.phar:
	curl -LS https://github.com/humbug/box/releases/download/3.8.4/box.phar -o bin/box.phar
	chmod a+x bin/box.phar
