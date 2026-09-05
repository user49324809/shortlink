# Сервис коротких ссылок + QR (Yii2 Basic)

Проект на стеке:
- Yii2 Basic
- PHP 8.1+
- MySQL / MariaDB
- jQuery
- Bootstrap 5
- QR генерируется локально, без API сторонних серверов

## Что умеет
- Принимает URL на главной странице
- Валидирует формат URL
- Проверяет доступность публичного ресурса через cURL
- Сохраняет ссылку в БД
- Генерирует короткий код и QR
- Возвращает результат через Ajax без перезагрузки страницы
- Делает редирект по короткой ссылке
- Ведёт логи переходов с внешним IP
- Считает количество переходов
- Имеет защищённую admin-страницу со списком ссылок, счетчиками и логами

## Быстрый старт



### 1. Установить зависимости
Клонировать проект:
```bash
git clone https://github.com/user49324809/shortlink.git
cd shortlink
```

```bash
composer install
```

### 2. Настроить подключение к БД
Скопируйте файл:
```bash
cp config/db.php.example config/db.php
```

Отредактируйте `config/db.php` или задайте переменные `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` и `DB_PASSWORD`.

### 3. Выполнить миграции
Настроить БД:
- создать базу `shortlink_qr`
- настроить `config/db.php`
```bash
php yii migrate
```

### 4. Запустить встроенный сервер
```bash
php yii serve --port=8080
```

Открыть:
```text
http://localhost:8080
```
### 5. Настроить административный доступ

Перед запуском задайте переменные окружения:

```dotenv
ADMIN_USERNAME=admin
ADMIN_PASSWORD=use-a-long-random-password
COOKIE_VALIDATION_KEY=use-another-long-random-value
TRUST_PROXY_HEADERS=false
```

В production включайте `TRUST_PROXY_HEADERS=true` только за доверенным reverse proxy, который перезаписывает клиентские заголовки.

### Запуск в Docker

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app php yii migrate --interactive=0
```
## Альтернатива: Nginx / Apache
DocumentRoot должен указывать на папку `web/`.

## Структура таблиц
Используются 2 таблицы:
- `short_url` — исходный URL, короткий код, счетчик, timestamps
- `short_url_hit` — лог переходов, IP, User-Agent, Referer, дата

## Маршруты
- `/` — форма генерации
- `/admin` — admin-страница со списком ссылок и логов, защищённая HTTP Basic Authentication
- `/site/shorten` — Ajax-обработчик
- `/{code}` — редирект по короткой ссылке

## Проверка доступности
Выполняется локально через cURL. Внешние API не используются. Локальные, приватные и зарезервированные IP-адреса блокируются; автоматический переход по редиректам отключён для защиты от SSRF.
Если сайт отвечает кодом `2xx` или `3xx`, URL считается доступным.

## Примечания
- При повторном вводе одного и того же URL используется уже существующая запись.
- QR-код встроен в страницу в формате base64 PNG.
- По умолчанию IP берётся только из `REMOTE_ADDR`. Заголовки `X-Forwarded-For` и `X-Real-IP` учитываются только при явном включении `TRUST_PROXY_HEADERS`.
