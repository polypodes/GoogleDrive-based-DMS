#
# Makefile
# Les Polypodes, 2014
# Licence: MIT
# Source: https://github.com/polypodes/Build-and-Deploy/blob/master/build/Makefile

# To enable this quality-related tasks, add these dependencies to your composer.json:
# they'll be available in the ./bin dir :
#
#    "require-dev": {
#	     (...)
#        "phpunit/phpunit":             "~3.7",
#        "squizlabs/php_codesniffer":   "2.0.x-dev",
#        "sebastian/phpcpd":            "*",
#        "phploc/phploc" :              "*",
#        "phpmd/phpmd" :                "2.0.*",
#        "pdepend/pdepend" :            "2.0.*",
#        "fabpot/php-cs-fixer":         "@stable"
#    },


# Usage:

# me@myserver$~: make help
# me@myserver$~: make install
# me@myserver$~: make reinstall
# me@myserver$~: make update
# me@myserver$~: make tests
# me@myserver$~: make quality
# etc.

############################################################################
# Vars

# some lines may be useless for now, but these are nice tricks:
PWD         := $(shell pwd)
# Retrieve db connection params, triming white spaces
DB_USER	    := $(shell if [ -f app/config/parameters.yml ] ; then cat app/config/parameters.yml | grep 'database_user' | sed 's/database_user: //' | sed 's/^ *//;s/ *$$//' ; fi)
DB_PASSWORD := $(shell if [ -f app/config/parameters.yml ] ; then cat app/config/parameters.yml | grep 'database_password' | sed 's/database_password: //' | sed 's/null//' | sed 's/^ *//;s/ *$$//' ; fi)
DB_NAME     := $(shell if [ -f app/config/parameters.yml ] ; then cat app/config/parameters.yml | grep 'database_name' | sed 's/database_name: //' | sed 's/^ *//;s/ *$$//' ; fi)
VENDOR_PATH := $(PWD)/vendor
BIN_PATH    := $(PWD)/bin
WEB_PATH    := $(PWD)/web
NOW         := $(shell date +%Y-%m-%d--%H-%M-%S)
REPO        := "https://github.com/polypodes/GoogleDrive-based-DMS.git"
BRANCH      := 'master'
# Colors
YELLOW      := $(shell tput bold ; tput setaf 3)
GREEN       := $(shell tput bold ; tput setaf 2)
RESETC      := $(shell tput sgr0)

# Custom MAKE options
ifndef VERBOSE
  MAKEFLAGS += --no-print-directory
endif

############################################################################
# Mandatory tasks:

all: .git/hook/pre-commit web/bower_components vendor/autoload.php check help

vendor/autoload.php:
	@composer self-update
	@composer install --optimize-autoloader

web/bower_components:
	@cd web; bower install; cd -;

.git/hook/pre-commit:
	@curl -s -o .git/hooks/pre-commit https://raw.githubusercontent.com/polypodes/Build-and-Deploy/master/hooks/pre-commit
	@chmod +x .git/hooks/pre-commit

############################################################################
# Specific, project-related sf2 tasks:

integration:
	@echo
	@cd integration
	@gulp build
	@cd ../

############################################################################
# Generic sf2 tasks:

help:
	@echo "\n${GREEN}Usual tasks:${RESETC}\n"
	@echo "\tTo prepare install:\tmake"
	@echo "\tTo install:\t\tmake install"
	@echo "\tTo update from git:\tmake update"
	@echo "\tTo reinstall:\t\tmake reinstall (will erase all your datas)\n\n"

	@echo "${GREEN}Other specific tasks:${RESETC}\n"
	@echo "\tTo check code quality:\tmake quality"
	@echo "\tTo fix code style:\tmake cs-fix"
	@echo "\tTo clear all caches:\tmake clear"
	@echo "\tTo run tests:\t\tmake tests (will erase all your datas)\n"

check:
	@php app/check.php

pull:
	@git pull origin $(BRANCH)

assets:
	@echo "\nPublishing assets..."
	@php app/console assets:install --symlink

clear: vendor/autoload.php
	@echo
	@echo "Resetting caches..."
	@php app/console cache:clear --env=prod --no-debug
	@php app/console cache:clear --env=dev

explain:
	@echo "git pull origin master + update db schema + build integration + copy new assets + rebuild prod cache"
	@echo "Note you can change the git remote repo username in .git/config"

behavior: vendor/autoload.php
#	@echo "Run behavior tests..."
#	@bin/behat --lang=fr  "@AcmeDemoBundle"

unit: vendor/autoload.php
	@echo "Run unit tests..."
	@php bin/phpunit -c app -v

codecoverage: vendor/autoload.php
	@echo "Run coverage tests..."
	@bin/phpunit -c app -v --coverage-html ./build/codecoverage

continuous: vendor/autoload.php
	@echo "Starting continuous tests..."
	@while true; do bin/phpunit -c build/phpunit.xml -v; done

sniff: vendor/autoload.php
	@bin/phpcs --standard=PSR2 src -n

dry-fix:
	@bin/php-cs-fixer fix . --config=sf23 --dry-run -vv

cs-fix:
	@bin/phpcbf --standard=PSR2 src
	@bin/php-cs-fixer fix . --config=sf23 -vv

#quality must remain quiet, as far as it's used in a pre-commit hook validation
quality: sniff dry-fix

build:
	@mkdir -p build/pdepend

# packagist-based dev tools to add to your composer.json. See http://phpqatools.org
stats: quality build
	@echo "Some stats about code quality"
	@bin/phploc src
	@bin/phpcpd src
	@bin/phpmd src text codesize,unusedcode
	@bin/pdepend --summary-xml=build/pdepend/summary.xml --jdepend-chart=build/pdepend/jdepend.svg --overview-pyramid=build/pdepend/pyramid.svg src

update: vendor/autoload.php
	@$(MAKE) explain
	@$(MAKE) pull
	@$(MAKE) clear
	@$(MAKE) done

robot:
	@echo "User-agent: *" > $(WEB_PATH)/robots.txt
	@echo "Disallow: " >> $(WEB_PATH)/robots.txt

unrobot:
	@echo "User-agent: *" > $(WEB_PATH)/robots.txt
	@echo "Disallow: /" >> $(WEB_PATH)/robots.txt

done:
	@echo
	@echo "${GREEN}Done.${RESETC}"

# Tasks sets:

install: all assets clear done

tests: behavior unit codecoverage

deploy: vendor/autoload.php
	@$(MAKE) explain
	@$(MAKE) pull
#	@$(MAKE) schemaDb
	@$(MAKE) clear
	@$(MAKE) done

############################################################################
# .PHONY tasks list

.PHONY: integration data fixtures help check all pull dropDb createDb myqldump
.PHONY: schemaDb assets clear explain behavior unit codecoverage
.PHONY: continuous sniff dry-fix cs-fix quality stats deploy done prepareDb purgeDb
.PHONY: install reinstall test update robot unrobot
# vim:ft=make
