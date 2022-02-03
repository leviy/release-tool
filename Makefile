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

security-tests: vendor bin/local-security-checker
	bin/local-security-checker

security_checker_binary = local-php-security-checker_1.0.0_linux_amd64
ifeq ($(shell uname -s), Darwin)
	security_checker_binary = local-php-security-checker_1.0.0_darwin_amd64
endif

bin/local-security-checker:
	curl -LS https://github.com/fabpot/local-php-security-checker/releases/download/v1.0.0/$(security_checker_binary) -o bin/local-security-checker
	chmod a+x bin/local-security-checker

bin/box.phar:
	curl -LS https://github.com/humbug/box/releases/download/3.14.0/box.phar -o bin/box.phar
	chmod a+x bin/box.phar
