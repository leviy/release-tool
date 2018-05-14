.PHONY: all check

all: vendor

vendor: composer.json $(wildcard composer.lock)
	composer install

check: vendor
	vendor/bin/phpcs -p --colors
	vendor/bin/phpmd src/ text phpmd.xml
