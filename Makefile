.PHONY: all check static-analysis coding-standards

all: vendor

vendor: composer.json $(wildcard composer.lock)
	composer install

check: static-analysis coding-standards

static-analysis: vendor
	vendor/bin/phpstan analyse --level=7 src/

coding-standards: vendor
	vendor/bin/phpcs -p --colors
	vendor/bin/phpmd src/ text phpmd.xml
