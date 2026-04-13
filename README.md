# PHP with Redis and RabbitMQ

Este es un pequeño proyecto de ejemplo para probar la integración de PHP con Redis y RabbitMQ utilizando Docker.

## Requisitos

- Docker
- Docker Compose
- Make (opcional, para usar el Makefile)

## Uso Rápido (Makefile)

Si tienes `make` instalado, puedes usar los siguientes atajos:
- `make build`: Construye e inicia todo.
- `make install`: Instala dependencias.
- `make test-redis`: Prueba Redis (String simple).
- `make test-redis-types`: Prueba completa con Strings, Sets, Lists y Hashes.
- `make test-producer` / `make test-consumer`: Pruebas de RabbitMQ.
- `make shell`: Acceder al contenedor PHP.
- `make help`: Ver todos los comandos disponibles.

## Configuración Manual

1.  **Levantar los contenedores:**
    ```bash
    docker-compose up -d
    ```

2.  **Instalar dependencias de PHP:**
    ```bash
    docker-compose exec php composer install
    ```

## Ejemplos de Pruebas

### 1. Redis
Para probar la conexión con Redis y realizar una operación simple de set/get:
```bash
docker-compose exec php php src/redis_test.php
```

### 2. RabbitMQ (Producción y Consumo)

Abre dos terminales para ver el flujo en tiempo real:

**Terminal 1 (Consumidor):**
```bash
docker-compose exec php php src/rabbit_consumer.php
```

**Terminal 2 (Productor):**
```bash
docker-compose exec php php src/rabbit_producer.php
```

### 3. Paneles de Administración

- **RabbitMQ Management:** [http://localhost:15672](http://localhost:15672) (Usuario/Password: `guest`/`guest`)
- **Redis Commander (Web UI):** [http://localhost:8081](http://localhost:8081)

## Tests de Estrés (RabbitMQ)

Para probar el rendimiento y cómo RabbitMQ gestiona grandes volúmenes de datos, he incluido scripts de estrés.

### Ejecución
1.  **Lanzar el consumidor:** `make stress-consumer` (esperará mensajes).
2.  **Lanzar el productor:** `make stress-producer` (enviará 10,000 mensajes).

### Qué observar
-   **Velocidad (msgs/sec):** Ambos scripts calculan la velocidad media de envío y procesamiento.
-   **Dashboard:** En el panel de RabbitMQ, verás picos en la gráfica de `Message rates`.
-   **Backlog:** Si cierras el consumidor y lanzas el productor varias veces, verás cómo la cola se llena. Luego, al abrir el consumidor, verás cómo baja el tráfico progresivamente.
- **Redis CLI (desde Terminal):** Disponible mediante los comandos detallados abajo.

## Gestión de Redis desde CLI

Puedes interactuar directamente con Redis usando la herramienta `redis-cli` incluida en la imagen de Redis.

### 1. Acceder a la consola interactiva:
```bash
docker compose exec redis redis-cli
```

### 2. Comandos por tipo de dato:

#### Strings (Cadenas simples)
- `SET user:1 "Joan"`: Guarda un valor.
- `GET user:1`: Recupera el valor.
- `INCR contador`: Incrementa un valor numérico.

#### Sets (Conjuntos - valores únicos sin orden)
- `SADD mi_set "valor1" "valor2"`: Añade elementos al conjunto.
- `SMEMBERS mi_set`: Muestra todos los miembros del conjunto.
- `SISMEMBER mi_set "valor1"`: Comprueba si existe el valor (devuelve 1 o 0).
- `SREM mi_set "valor1"`: Elimina un elemento.

#### Lists (Listas - ordenadas por inserción)
- `LPUSH mi_lista "item1"`: Añade al inicio.
- `RPUSH mi_lista "item2"`: Añade al final.
- `LRANGE mi_lista 0 -1`: Muestra todos los elementos.
- `LPOP mi_lista`: Saca y devuelve el primer elemento.

#### Hashes (Objetos/Diccionarios)
- `HSET user:1 name "Joan" email "joan@example.com"`: Guarda campos de un objeto.
- `HGET user:1 name`: Recupera un campo específico.
- `HGETALL user:1`: Recupera todo el objeto.

### 3. Comandos de utilidad general:
- `PING`: Devuelve `PONG` si la conexión está activa.
- `KEYS *`: Lista todas las llaves guardadas.
- `EXISTS mi_llave`: Comprueba si una llave existe.
- `EXPIRE mi_llave 60`: Define que la llave expire en 60 segundos.
- `TTL mi_llave`: Muestra cuánto tiempo le queda de vida.
- `DEL mi_llave`: Borra una llave.
- `FLUSHALL`: Borra TODO el contenido de Redis.
- `MONITOR`: Muestra en tiempo real todas las peticiones recibidas.

### 4. Comandos rápidos (sin entrar a la consola):
```bash
docker compose exec redis redis-cli KEYS "*"
docker compose exec redis redis-cli GET test_key
```

## Estructura del Proyecto

- `docker-compose.yml`: Definición de los servicios.
- `Dockerfile`: Configuración del entorno PHP (incluye extensiones `bcmath` y `sockets`).
- `composer.json`: Dependencias (`predis/predis` y `php-amqplib/php-amqplib`).
- `src/`:
    - `redis_test.php`: Script de prueba para Redis.
    - `rabbit_producer.php`: Envía mensajes a una cola de RabbitMQ.
    - `rabbit_consumer.php`: Escucha y procesa mensajes de la cola.
