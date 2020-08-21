.DEFAULT_GOAL := black@hole
CONSOLE       := bin/console
COMPOSER      := composer
SYMFONY       := symfony
CS_FIXER      := ./vendor/bin/php-cs-fixer
PHPUNIT       := ./bin/phpunit

# inject env file
include .env
export $(shell sed 's/=.*//' .env)

install: export COMPOSER_MEMORY_LIMIT=-1
install:
	$(COMPOSER) install
	$(PHPUNIT) install

cs:
	$(CS_FIXER) fix --diff --dry-run

cs-fix:
	$(CS_FIXER) fix --diff

check:
	$(COMPOSER) check
	$(SYMFONY) check:security
	#$(COMPOSER) validate --strict
	#$(CONSOLE) doctrine:schema:validate --skip-sync -vvv --no-interaction
	#$(CONSOLE) debug:container --deprecations

lint:
	$(CONSOLE) lint:yaml config --parse-tags
	$(CONSOLE) lint:container

generate-jwt-keys:
	mkdir -p config/jwt
	grep '^JWT_PASSPHRASE=' .env | cut -f 2 -d '=' | openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass stdin
	grep '^JWT_PASSPHRASE=' .env | cut -f 2 -d '=' | openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin stdin

init-db:
	$(CONSOLE) doctrine:database:create
	$(CONSOLE) doctrine:schema:create

#test: export SYMFONY_PHPUNIT_DIR=./bin/.phpunit
#test: export SYMFONY_DEPRECATIONS_HELPER=disabled
test: export SYMFONY_DEPRECATIONS_HELPER=999999
test:
	$(eval include .env.test)
	$(eval export sed 's/=.*//' .env.test)
	$(CONSOLE) doctrine:database:drop --force
	$(CONSOLE) doctrine:database:create
	$(CONSOLE) doctrine:schema:create
	$(CONSOLE) doctrine:fixtures:load --no-interaction
	$(PHPUNIT)
