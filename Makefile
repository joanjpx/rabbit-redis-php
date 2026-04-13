.PHONY: up down restart build install shell redis-cli test-redis test-producer test-consumer logs help

# Variables
DOCKER_COMPOSE = docker compose
PHP_CONT = $(DOCKER_COMPOSE) exec php
REDIS_CONT = $(DOCKER_COMPOSE) exec redis

help: ## Muestra este mensaje de ayuda
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

up: ## Levanta los contenedores en segundo plano
	$(DOCKER_COMPOSE) up -d

down: ## Detiene y elimina los contenedores
	$(DOCKER_COMPOSE) down

restart: ## Reinicia los contenedores
	$(DOCKER_COMPOSE) restart

build: ## Reconstruye y levanta los contenedores
	$(DOCKER_COMPOSE) up -d --build

install: ## Instala las dependencias de composer
	$(PHP_CONT) composer install

shell: ## Entra a la terminal del contenedor PHP
	$(PHP_CONT) bash

redis-cli: ## Entra a la consola de Redis
	$(REDIS_CONT) redis-cli

test-redis: ## Ejecuta la prueba de Redis
	$(PHP_CONT) php src/redis_test.php

test-producer: ## Ejecuta el productor de RabbitMQ
	$(PHP_CONT) php src/rabbit_producer.php

test-consumer: ## Ejecuta el consumidor de RabbitMQ
	$(PHP_CONT) php src/rabbit_consumer.php

stress-producer: ## Ejecuta el productor de estrés (10k mensajes)
	$(PHP_CONT) php src/rabbit_stress_producer.php

stress-consumer: ## Ejecuta el consumidor de estrés
	$(PHP_CONT) php src/rabbit_stress_consumer.php

logs: ## Muestra los logs en tiempo real
	$(DOCKER_COMPOSE) logs -f
