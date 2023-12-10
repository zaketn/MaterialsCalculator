# Materials Сalculator
## Установка
В проекте настроен Docker
1. Проверить в docker-compose.yml доступность локальных портов которые будут занимать сервисы
2. Создать .env на основе .env.example, по необходимости поменять требуемые значения(можно ничего не менять по умолчанию)
3. `docker compose run --rm node install`
4. `docker compose up -d`
5. `docker compose exec php sh` - войти в php контейнер
6. `php artisan key:generate`
7. `exit` - выйти из php контейнера
