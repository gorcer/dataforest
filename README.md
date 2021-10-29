## About

The service allows you to collect data from various sources and display it in a convenient form. Capabilities for data collection:

- parsing a number from a page on the Internet
- fetching data with a query from the database
- obtaining data on API (under development)

Display options:

- Charts with data grouped by days, weeks, months, years
- Table
- Json
- Last relevant value

The result can be used in the API of other services, a public link is provided.

-----------

## О проекте

Сервис позволяет собирать данные из разных источников и отображать их в удобном виде.

Возможности для сбора данных:
- парсинг числа из страницы в интернете
- выборка данных запросом из БД
- получение данных по АПИ (в разработке)

Возможности отображения:
- График с группировкой по дням, неделям, месяцам, годам
- Таблица
- JSON
- Последнее актуальнео значение

Результат может быть использован в АПИ других сервисов, публичная ссылка предоставляется.

<img src='https://debin.ru/img/sample.png' width='400px'/>


Попробовать вживую можно тут https://debin.ru

## Как установить на свой сервер
- убедится что на сервере есть MySQL, Mongo, PHP^7.2
- склонировать
- создать базу данных
- заполнить настройки подключения в .env
- composer update
- php artisan key:generate
- php artisan migrate




