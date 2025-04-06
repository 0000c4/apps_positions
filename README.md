## Функциональность
- Сбор и сохранение данных о позициях приложения в топе категорий
- API-эндпоинт для получения позиций приложения за указанную дату
- Ограничение количества запросов с одного IP-адреса (5 запросов/минуту)
- Логирование всех запросов к API
- 
## Установка

1. Клонируйте репозиторий
```bash
git clone https://github.com/0000c4/apps_positions.git
cd apps_positions
```

2. Установите зависимости
```bash
composer install
```

3. Скопируйте `.env.example` в `.env` и настройте подключение к базе данных
```bash
cp .env.example .env
php artisan key:generate
```

4. Выполните миграции
```bash
php artisan migrate
```

## Использование

### Загрузка данных

Для загрузки данных за определенную дату используйте команду:

```bash
php artisan app:fetch-top-positions 2025-03-20
```

### API-эндпоинт

Запрос:
```
GET /appTopCategory?date=2025-03-20
```

Ответ:
```json
{
    "status_code": 200,
    "message": "ok",
    "data": {
        "2": 1,
        "23": 1,
        "134": 2
    }
}
```

## Структура проекта

- `app/Models/AppTopPosition.php` - модель для работы с данными о позициях
- `app/Console/Commands/FetchAppTopPositions.php` - команда для загрузки данных
- `app/Http/Controllers/AppTopCategoryController.php` - контроллер API-эндпоинта
- `app/Http/Middleware/ThrottleAppTopCategory.php` - ограничение запросов
- `app/Http/Middleware/LogAppTopCategoryRequests.php` - логирование запросов
