# О проекте
Интернет магазин автомобилей AutoBit. Разработан командой 2 в рамках 2 модуля курса Веб-разработчик Битрикс24//Университет 2023
***
# Системные требования
## Основные:
PHP 8.1
APACHE 2.4
MYSQL 5.7
## Для автотестирования:
установить phpunit и подключить guzzlehtml.
***
# Запуск проекта

1) Создать базу данных с произвольным названием

2) В папке /core/config создать файл config.local.php со следующими полями 
```{PHP} {
<?php 
return [ 
   	'DB_HOST' => '<Ваш IP адрес(для Apache сервера) или хост>', 
   	'DB_NAME' => '<Название базы данных>', 
   	'DB_USER' => '<Логин для доступа в БД>', 
   	'DB_PASSWORD' => ' <Пароль для доступа в БД> ', 
   	'DB_TABLE_MIGRATION' => 'migration', 
	]; 
```

3) При первом запуске сайта запустится миграция и добавятся тестовые данные

4) Готово! 
