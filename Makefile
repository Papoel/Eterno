# --------------------
# Paramètre du Makefile
# --------------------
PROJECT = Eterno
AUTHOR = Papoel
HTTP_PORT = 8000
HOST_NAME = 127.0.0.1
DB_NAME = db_eterno
DB_USER = root
DB_PASS =
DB_PORT = 3306
DB_VERSION = 11.0.2
SERVER_NAME = -MariaDB
DB_CHARSET = charset=utf8mb4

# La variable DATABASE_URL pour postgresql
# DATABASE_URL=postgresql://${DB_USER}:${DB_PASS}@${HOST_NAME}:${DB_PORT}/${DB_NAME}?serverVersion=${DB_VERSION}&${DB_CHARSET}
# La variable DATABASE_URL pour mysql
DATABASE_URL = mysql://${DB_USER}:${DB_PASS}@${HOST_NAME}:${DB_PORT}/${DB_NAME}?serverVersion=${DB_VERSION}${SERVER_NAME}&${DB_CHARSET}

# --------------------
# Commandes
# --------------------
# Commande PHP
PHP = php
# Commande Composer
COMPOSER = composer
# Commande Symfony
SYMFONY = $(PHP) bin/console
# Commande Yarn
YARN = yarn
# Binaire de Symfony
SYMFONY_BIN = symfony
# Linter de Symfony
SYMFONY_LINT = $(SYMFONY) lint:

# --------------------
# Commandes locales
# --------------------
# Commande Symfony locale
BREW = brew
DOCKER = docker
DOCKER_COMPOSE = docker-compose
DOCKER_RUN = $(DOCKER) run
PHP_CS_FIXER = php-cs-fixer
BIN_VENDOR = vendor/bin/
MAKE = make

# --------------------
# PHPQA
# --------------------
PHPQA = jakzal/phpqa:php8.2
PHPQA_RUN = $(DOCKER_RUN) --init --rm -v $(PWD):/project -w /project $(PHPQA)
# --------------------

# --------------------
# TESTS
# --------------------
PHPUNIT = APP_ENV=test $(SYMFONY_BIN) php bin/phpunit
PEST = ./$(BIN_VENDOR)pest
# --------------------


help: ## Afficher l'aide
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'


# --------------------
# Commandes du projet 📦
# --------------------
## ********** 📦 PROJET 📦 ******************************************************************
start: up serve open ## Lancer le projet | up + serve + open
stop: stop down ## Arrêter le projet | down
send-mail: ## Envoi d'email | php bin/console messenger:consume -vv
	$(SYMFONY) messenger:consume -vv

create-env: ## Création du fichier '.env.local' et création de DATABASE_URL
	@echo "Création du fichier .env.local et copie de APP_ENV et APP_SECRET en cours..."
	@if [ -f .env.local ]; then \
		sed 's|^DATABASE_URL=.*|DATABASE_URL="${DATABASE_URL}"|' .env.local > .env.local.tmp; \
		mv .env.local.tmp .env.local; \
	else \
		sed -n -e '/^APP_ENV/p' -e '/^APP_SECRET/p' -e '/^MESSENGER_TRANSPORT_DSN/p' .env > .env.local; \
		echo 'DATABASE_URL="${DATABASE_URL}"' >> .env.local; \
	fi
	@echo "La variable DATABASE_URL a été créée ou mise à jour dans le fichier .env.local avec succès !"

check-dump: ## Vérifier les occurrences de {{ dump() }} dans les fichiers du dossier 'templates'
	@echo "Vérification des occurrences de {{ dump() }} dans les fichiers du dossier 'templates'..."
	@if [ -d templates/ ]; then \
		files_with_dump=$$(grep -r -n "{{ dump(" templates/ 2>&1); \
		if [ -n "$$files_with_dump" ]; then \
			echo "\033[31mErreur : Des occurrences de {{ dump() }} ont été trouvées dans les fichiers suivants :\033[0m"; \
			echo "$$files_with_dump"; \
			exit 1; \
		else \
			echo "\033[32mAucune occurrence de {{ dump() }} n'a été trouvée dans les fichiers du dossier 'templates'.\033[0m"; \
		fi \
	else \
		echo "\033[31mLe dossier 'templates' n'existe pas. Assurez-vous qu'il est présent.\033[0m"; \
		exit 1; \
	fi

before-commit: ## S'assurer que le code est propre avant de commiter !
	$(MAKE) check-dump qa-cs-fixer qa-phpstan qa-security-checker qa-phpcpd qa-lint-twigs qa-lint-yaml qa-lint-container qa-lint-schema pest
.PHONY: check-dump before-commit

# --------------------
# Commandes Symfony 🐘
# --------------------
## ********** 🐘 SYMFONY 🐘 ******************************************************************
sf: ## Afficher toutes les commandes de Symfony | symfony
	$(SYMFONY)

install: ## Installer les dépendances | composer install
	$(COMPOSER) install

serve: ## Lancer le serveur | symfony server:start -d
	$(SYMFONY_BIN) server:start -d

stop: ## Arrêter le serveur | symfony server:stop
	$(SYMFONY_BIN) server:stop

open: ## Ouvrir le site | symfony open:local
	$(SYMFONY_BIN) open:local

server-restart: ## Redémarrer le serveur | stop + start
	stop start
.PHONY: server-restart

certificat: ## Installer le certificat | symfony server:ca:install
	$(SYMFONY_BIN) server:ca:install

# --------------------
# Commandes de debug
# --------------------
## ********** 🐛 DEBUG 🐛 ******************************************************************
debug: ## Afficher les variables d'environnement | symfony console debug:cotainer
	$(SYMFONY) debug:container

debug-routes: ## Afficher les routes | symfony console debug:router
	$(SYMFONY) debug:router

debug-config: ## Afficher la configuration | symfony console debug:config
	$(SYMFONY) debug:config

debug-env: ## Afficher les variables d'environnement | symfony console debug:config
	$(SYMFONY) debug:dotenv
	$(SYMFONY) debug:config

# --------------------
# Commandes de sécurité
# --------------------
## ********** 🔒 SÉCURITÉ 🔒 ******************************************************************
security-check: ## Vérifier les vulnérabilités | symfony console security:check
	$(SYMFONY) security:check

# --------------------
# Commandes de cache
# --------------------
## ********** 🗑️ CACHE 🗑️ ******************************************************************
cc: ## Vider le cache | symfony console cache:clear
	$(SYMFONY) cache:clear

cc-prod: ## Vider le cache de production | symfony console cache:clear --env=prod
	$(SYMFONY) cache:clear --env=prod

cc-test: ## Vider le cache de test | symfony console cache:clear --env=test
	$(SYMFONY) cache:clear --env=test

cc-warmup: ## Précharger le cache | symfony console cache:warmup
	$(SYMFONY) cache:warmup

purge: ## Supprimer le cache | rm -rf var/cache/* var/logs/*
	@rm -rf var/cache/* var/log/*

## ********** 🐳 DOCKER 🐳 ******************************************************************
up: ## Lancer Docker | docker-compose up -d
	$(DOCKER_COMPOSE) up -d

down: ## Arrêter Docker | docker-compose down --remove-orphans
	$(DOCKER_COMPOSE) down --remove-orphans

reboot: ## Redémarrer Docker | up + down
	$(MAKE) down
	$(MAKE) up
.PHONY: reboot

## ********** 🛠️ MAKER 🛠️ ******************************************************************
entity: ## Créer une entité | symfony console make:entity
	$(SYMFONY) make:entity

controller: ## Créer un contrôleur | symfony console make:controller
	$(SYMFONY) make:controller

form: ## Créer un formulaire | symfony console make:form
	$(SYMFONY) make:form

crud: ## Créer un CRUD | symfony console make:crud
	$(SYMFONY) make:crud

command: ## Créer une commande | symfony console make:command
	$(SYMFONY) make:command

test: ## Créer un test | symfony console make:test
	$(SYMFONY) make:test

subscriber: ## Créer un subscriber | symfony console make:subscriber
	$(SYMFONY) make:subscriber

# --------------------
# Qualité du code
# --------------------
## ********** 📝 QUALITÉ DU CODE 📝 ******************************************************************
# --------------------
# Utilisation de PHPQA
# --------------------
qa-cs-fixer-dry-run: ## Execute php-cs-fixer in dry-run mode.
	$(PHPQA_RUN) php-cs-fixer fix ./src --rules=@Symfony --verbose --dry-run
.PHONY: qa-cs-fixer-dry-run

qa-cs-fixer: ## Execute php-cs-fixer.
	$(PHPQA_RUN) php-cs-fixer fix ./src --rules=@Symfony --verbose
.PHONY: qa-cs-fixer

qa-phpstan: ## Execute phpstan.
	$(PHPQA_RUN) phpstan analyse --configuration=phpstan.neon
.PHONY: qa-phpstan

qa-security-checker: ## Verifier les dépendances avec security-checker.
	$(SYMFONY_BIN) security:check
.PHONY: qa-security-checker

qa-phpcpd: ## Execute phpcpd (Copier/Coller Detector).
	$(PHPQA_RUN) phpcpd ./src
.PHONY: qa-phpcpd

qa-php-metrics: ## Execute phpmetrics, génère un rapport html dans var/phpmetrics.
	$(PHPQA_RUN) phpmetrics --report-html=var/phpmetrics ./src
.PHONY: qa-php-metrics

qa-lint-twigs: ## Vérifier la syntaxe des fichiers twig.
	$(SYMFONY_LINT)twig templates
.PHONY: qa-lint-twigs

qa-lint-yaml: ## Vérifier la syntaxe des fichiers yaml.
	$(SYMFONY_LINT)yaml config
.PHONY: qa-lint-yaml

qa-lint-container: ## Vérifier la configuration du container.
	$(SYMFONY_LINT)container
.PHONY: qa-lint-container

qa-lint-schema: ## Vérifier la validité du schéma de la base de données.
	$(SYMFONY) doctrine:schema:validate
.PHONY: qa-lint-schema

qa-audit: ## Faire un audit de sécurité avec composer.
	$(COMPOSER) audit
.PHONY: qa-audit

# --------------------
# Commandes de base de données
# --------------------
## ********** 🗄️  BASE DE DONNÉES 🗄️ ******************************************************************
db-create-test: ## Créer la base de données de test | symfony console doctrine:database:create --env=test
	$(SYMFONY) doctrine:database:create --env=test

db-drop-test: ## Supprimer la base de données de test | symfony console doctrine:database:drop --force --env=test
	$(SYMFONY) doctrine:database:drop --force --env=test

db-schema: ## Créer le schéma de la base de données | symfony console doctrine:schema:create
	$(SYMFONY) doctrine:schema:create

db-schema-test: ## Créer le schéma de la base de données de test | symfony console doctrine:schema:create --env=test
	$(SYMFONY) doctrine:schema:create --env=test

db-drop-schema: ## Supprimer le schéma de la base de données | symfony console doctrine:schema:drop --force
	$(SYMFONY) doctrine:schema:drop --force --if-exists

db-drop-schema-test: ## Supprimer le schéma de la base de données de test | symfony console doctrine:schema:drop --force --env=test
	$(SYMFONY) doctrine:schema:drop --force --env=test

db-s-v: ## Valider le schéma de la base de données | symfony console doctrine:schema:validate
	$(SYMFONY) doctrine:schema:validate

db-s-u: ## Mettre à jour le schéma de la base de données | symfony console doctrine:schema:update --force
	$(SYMFONY) doctrine:schema:update --force

db-s-u-test: ## Mettre à jour le schéma de la base de données de test | symfony console doctrine:schema:update --force --env=test
	$(SYMFONY) doctrine:schema:update --force --env=test

schema-load-db: ## Charger le schéma de la base de données | schema-validate-db + schema-update-db
	schema-validate-db schema-update-db

doctrine-help: ## Afficher toutes les commandes de Doctrine | doctrine
	$(SYMFONY) doctrine

migration: ## Créer une migration | symfony console make:migration
	$(SYMFONY) make:migration

migrate: ## Exécuter les migrations | symfony console doctrine:migrations:migrate -n
	$(SYMFONY) doctrine:migrations:migrate -n

fixtures: ## Créer des fixtures | symfony console make:fixtures
	$(SYMFONY) make:fixtures

init-db:
	$(MAKE) cc
	$(MAKE) db-drop
	$(MAKE) db-create
	@if [ -n "$$(find migrations -name 'Version*' -print -quit)" ]; then \
		$(SYMFONY) doctrine:migrations:migrate --no-interaction; \
	else \
		$(SYMFONY) doctrine:schema:validate; \
		$(SYMFONY) doctrine:schema:update --force; \
	fi
	@read -p "Charger les Fixtures ? (y/n): " answer; \
	if [ "$$answer" = "y" ]; then \
		$(MAKE) fixtures-load; \
	else \
		echo "Exiting..."; \
		exit 0; \
	fi

db-drop: ## Supprimer la base de données | symfony console doctrine:database:drop --force
	$(SYMFONY) doctrine:database:drop --if-exists --force

db-create: ## Créer la base de données | symfony console doctrine:database:create
	$(SYMFONY) doctrine:database:create

fixtures-load:
	$(SYMFONY) doctrine:fixtures:load --no-interaction
.PHONY: init-db db-drop db-create fixtures-load

delete-db: ## Supprimer la base de données | db-drop-schema + db-drop
	$(MAKE) db-drop-schema
	$(MAKE) db-drop

init-db-tests: ## Initialiser la base de données de test | create-db-tests + schema-load-db
	$(MAKE) cc-test db-drop-test db-create-test db-schema-test db-s-v db-s-u-test

init-db-tests-with-fixtures: ## Initialiser la base de données de test avec les fixtures | init-db-tests + fixtures-load
	$(MAKE) init-db-tests
	$(SYMFONY) doctrine:fixtures:load --no-interaction --env=test


# --------------------
# Commandes de tests
# --------------------
## ********** 🧪 TESTS 🧪 ******************************************************************
pest : ## Exécuter les tests avec Pest | ./vendor/bin/pest
	$(PEST) --bail # --bail arrête les tests à la première erreur

pest-c: ## Vérifier la couverture minimum des test (80%)
	$(PEST) --coverage --min=80

pest-e: ## Créer un rapport de couverture des tests avec Pest.
	$(PEST) --coverage --coverage-html var/coverage
