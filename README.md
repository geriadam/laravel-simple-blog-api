# Simple API using Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling

## Docker for local development

1. Copy and rename `.env.example` to `.env`

2. build your docker images

```
docker compose up -d --build
```

4. Import your sql into database, you can find the host, user and database name inside `.env` file

when migration

```
DB_HOST=127.0.0.1
```

when normally

```
DB_HOST=db
```

5. Run

```
docker-compose exec app composer install
```

```
docker-compose exec app composer dump-autoload && php artisan key:generate
```

```
docker-compose exec app composer dump-autoload && php artisan migrate:refresh --seed
```

```
docker-compose exec app composer dump-autoload && php artisan passport:install
```

### API Doc

https://documenter.getpostman.com/view/9397278/2s8Z6scGJ4
