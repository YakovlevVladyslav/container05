# container05

## Цель работы.

Выполнив данную работу студент сможет подготовить образ контейнера для запуска веб-сайта на базе Apache HTTP Server + PHP (mod_php) + MariaDB.

## Задание.

Создать Dockerfile для сборки образа контейнера, который будет содержать веб-сайт на базе Apache HTTP Server + PHP (mod_php) + MariaDB. База данных MariaDB должна храниться в монтируемом томе. Сервер должен быть доступен по порту 8000.

Установить сайт WordPress. Проверить работоспособность сайта.

## Ход работы

### Извлечение конфигурационных файлов apache2, php, mariadb из контейнера

Создать в папке containers05 папку files, а также

- папку files/apache2 - для файлов конфигурации apache2;
- папку files/php - для файлов конфигурации php;
- папку files/mariadb - для файлов конфигурации mariadb.

Создать в папке containers05 файл Dockerfile со следующим содержимым:
```
# create from debian image
FROM debian:latest

# install apache2, php, mod_php for apache2, php-mysql and mariadb
RUN apt-get update && \
    apt-get install -y apache2 php libapache2-mod-php php-mysql mariadb-server && \
    apt-get clean
```
Построить образ контейнера с именем apache2-php-mariadb.
```
docker build -t apache2-php-mariadb .
```
- . - текущая папка

Создать контейнер apache2-php-mariadb из образа apache2-php-mariadb и запустить его в фоновом режиме с командой запуска bash.
```
docker run -d --name apache2-php-mariadb apache2-php-mariadb bash
```
- -d - detached mode, запуск в фоновом режиме текущего терминала, контейнер не принимает ввод/вывод.    

Скопировать из контейнера файлы конфигурации apache2, php, mariadb в папку files/ на компьютере. Для этого, в контексте проекта, выполнить команды:
```
docker cp apache2-php-mariadb:/etc/apache2/sites-available/000-default.conf files/apache2/
docker cp apache2-php-mariadb:/etc/apache2/apache2.conf files/apache2/
docker cp apache2-php-mariadb:/etc/php/8.4/apache2/php.ini files/php/
docker cp apache2-php-mariadb:/etc/mysql/mariadb.conf.d/50-server.cnf files/mariadb/
```

После выполнения команд в папке files/ появились файлы конфигурации apache2, php, mariadb. Необходимо остановить и удалить контейнер apache2-php-mariadb.
```
docker stop apache2-php-mariadb
docker rm apache2-php-mariadb
```

### Настройка конфигурационных файлов

#### Конфигурационный файл apache2

Открыть файл files/apache2/000-default.conf, найти строку #ServerName www.example.com и заменить её на ServerName localhost.

Найдите строку ServerAdmin webmaster@localhost и заменить в ней почтовый адрес на свой.

После строки DocumentRoot /var/www/html добавить следующие строки:
```
DirectoryIndex index.php index.html
```
- файлы, предоставляемые в качестве ответа сервером, если запрошена папка без указания конкретного документа (http://localhost/)

Сохранить файл и закрыть.
```
<VirtualHost *:80>
	# The ServerName directive sets the request scheme, hostname and port that
	# the server uses to identify itself. This is used when creating
	# redirection URLs. In the context of virtual hosts, the ServerName
	# specifies what hostname must appear in the request's Host: header to
	# match this virtual host. For the default virtual host (this file) this
	# value is not decisive as it is used as a last resort host regardless.
	# However, you must set it for any further virtual host explicitly.
	ServerName localhost

	ServerAdmin yakovlevvladyslav6@gmail.com
	DocumentRoot /var/www/html
	DirectoryIndex index.php index.html

	# Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
	# error, crit, alert, emerg.
	# It is also possible to configure the loglevel for particular
	# modules, e.g.
	#LogLevel info ssl:warn

	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined

	# For most configuration files from conf-available/, which are
	# enabled or disabled at a global level, it is possible to
	# include a line for only one particular virtual host. For example the
	# following line enables the CGI configuration for this host only
	# after it has been globally disabled with "a2disconf".
	#Include conf-available/serve-cgi-bin.conf
</VirtualHost>

```

В конце файла files/apache2/apache2.conf добавьте следующую строку:
```
ServerName localhost
```
- global default server name

#### Конфигурационный файл php

Открыть файл files/php/php.ini, найти строку ;error_log = php_errors.log и заменить её на error_log = /var/log/php_errors.log.

Настроить параметры memory_limit, upload_max_filesize, post_max_size и max_execution_time следующим образом:

memory_limit = 128M
upload_max_filesize = 128M
post_max_size = 128M
max_execution_time = 120
Сохранить файл и закрыть.

#### Конфигурационный файл mariadb

Открыть файл files/mariadb/50-server.cnf, найти строку #log_error = /var/log/mysql/error.log и раскомментировать её.

Сохранить файл и закрыть.

### Создание скрипта запуска

Создать в папке files папку supervisor и файл supervisord.conf со следующим содержимым:

```
[supervisord]
nodaemon=true
logfile=/dev/null
user=root

# apache2
[program:apache2]
command=/usr/sbin/apache2ctl -D FOREGROUND
autostart=true
autorestart=true
startretries=3
stderr_logfile=/proc/self/fd/2
user=root

# mariadb
[program:mariadb]
command=/usr/sbin/mariadbd --user=mysql
autostart=true
autorestart=true
startretries=3
stderr_logfile=/proc/self/fd/2
user=mysql
```
supervisor нужен для управления несколькими приложениями в одном контейнере. Устанавливает режим контейнера в foreground, перезапускает приложения в случае краша.

### Создание Dockerfile

Открыть файл Dockerfile и добавить в него следующие строки:

после инструкции FROM ... добавьте монтирование томов:
```
# mount volume for mysql data
VOLUME /var/lib/mysql

# mount volume for logs
VOLUME /var/log
```
в инструкции RUN ... добавьте установку пакета supervisor.
```
# install apache2, php, mod_php for apache2, php-mysql and mariadb, supervisor
RUN apt-get update && \
    apt-get install -y supervisor && \
    apt-get install -y apache2 php libapache2-mod-php php-mysql mariadb-server && \
    apt-get clean
```

после инструкции RUN ... добавьте копирование и распаковку сайта WordPress:
```
# add wordpress files to /var/www/html
ADD https://wordpress.org/latest.tar.gz /var/www/html/
```

после копирования файлов WordPress добавьте копирование конфигурационных файлов apache2, php, mariadb, а также скрипта запуска:
```
# copy the configuration file for apache2 from files/ directory
COPY files/apache2/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY files/apache2/apache2.conf /etc/apache2/apache2.conf

# copy the configuration file for php from files/ directory
COPY files/php/php.ini /etc/php/8.4/apache2/php.ini

# copy the configuration file for mysql from files/ directory
COPY files/mariadb/50-server.cnf /etc/mysql/mariadb.conf.d/50-server.cnf

# copy the supervisor configuration file
COPY files/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
```

для функционирования mariadb создайте папку /var/run/mysqld и установите права на неё:
```
# create mysql socket directory
RUN mkdir /var/run/mysqld && chown mysql:mysql /var/run/mysqld
```
откройте порт 80.
```
# open port 80
EXPOSE 80
```

добавьте команду запуска supervisord:
```
# start supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

Пересоберите образ контейнера с именем apache2-php-mariadb и запустите контейнер apache2-php-mariadb из образа apache2-php-mariadb.
```
docker run -it -p 8080:80 --name apache2-php-mariadb apache2-php-mariadb bash
``` 
Проверьте наличие сайта WordPress в папке /var/www/html/. Проверьте изменения конфигурационного файла apache2.
```
ls /var/www/html/
output:
index.html  latest.tar.gz
```
Сайт wordpress лежит в архиве latest.tar.gz

Скачивание архива было перемещено в папку temp, а разархивирование сайта производится в /var/www/html/
```
# add wordpress files to /var/www/html
# 1. Download the archive to a temporary directory
ADD https://wordpress.org/latest.tar.gz /tmp/wordpress.tar.gz

# 2. Extract wordpress directly into /var/www/html/ and clean up
RUN tar -xzf /tmp/wordpress.tar.gz -C /var/www/html/ && \
    rm /tmp/wordpress.tar.gz && \
    chown -R www-data:www-data /var/www/html/wordpress	
```
- x extract, z unzip, f next argument is file name
- -C change directory to unzip into
- chown change owner
- -R recursive
- www-data default group and user for web server

Проверьте изменения конфигурационного файла apache2.
```
nano /etc/apache2/apache2.conf
# В конце файла появился ServerName localhost
```

### Создание базы данных и пользователя

Создать базу данных wordpress и пользователя wordpress с паролем wordpress в контейнере apache2-php-mariadb. Для этого, в контейнере apache2-php-mariadb, выполнить команды:

```mysql
mysql -u root

CREATE DATABASE wordpress;
CREATE USER 'wordpress'@'localhost' IDENTIFIED BY 'wordpress';
GRANT ALL PRIVILEGES ON wordpress.* TO 'wordpress'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Создание файла конфигурации WordPress

Откройте в браузере сайт WordPress по адресу http://localhost/. 
```
http://localhost:8080/wordpress
```

Укажите параметры подключения к базе данных:

имя базы данных: wordpress;
имя пользователя: wordpress;
пароль: wordpress;
адрес сервера базы данных: localhost;
префикс таблиц: wp_.

Скопируйте содержимое файла конфигурации в файл files/wp-config.php на компьютере.
```
docker cp apache2-php-mariadb:/var/www/html/wordpress/wp-config.php files/wp-config.php
```

### Добавление файла конфигурации WordPress в Dockerfile
Добавьте в файл Dockerfile следующие строки:

```
# copy the configuration file for wordpress from files/ directory
COPY files/wp-config.php /var/www/html/wordpress/wp-config.php
```

## Запуск и тестирование

Пересоберите образ контейнера с именем apache2-php-mariadb
```
docker rm -f apache2-php-mariadb
docker build --no-cache -t apache2-php-mariadb .
```

запустите контейнер apache2-php-mariadb из образа apache2-php-mariadb.
```
docker run -d -p 8080:80 --name apache2-php-mariadb apache2-php-mariadb
```

Проверьте работоспособность сайта WordPress.

Сайт работает.

## Выводы.

В ходе работы был собран Docker-контейнер с Apache, PHP и MariaDB и развернут сайт WordPress. Выполнена настройка конфигураций сервисов, создана база данных и пользователь, а также настроено управление процессами через supervisor. В результате получен рабочий веб-сервер с доступным WordPress.

## Контрольные вопросы

1. Какие файлы конфигурации были изменены?

Были изменены конфигурационные файлы Apache (000-default.conf, apache2.conf), PHP (php.ini), MariaDB (50-server.cnf), а также добавлен файл конфигурации WordPress (wp-config.php) и конфигурация supervisor (supervisord.conf).

2. За что отвечает инструкция DirectoryIndex в файле конфигурации apache2?

DirectoryIndex задаёт порядок файлов, которые Apache открывает по умолчанию при обращении к директории (например, сначала index.php, затем index.html).

3. Зачем нужен файл wp-config.php?

wp-config.php содержит основные настройки WordPress, включая параметры подключения к базе данных (имя БД, пользователь, пароль, хост), а также базовые конфигурации сайта.

4. За что отвечает параметр post_max_size в файле конфигурации php?

post_max_size ограничивает максимальный размер данных, которые можно отправить на сервер методом POST (например, при загрузке форм или файлов).

5. Укажите, на ваш взгляд, какие недостатки есть в созданном образе контейнера?

Недостатком является объединение нескольких сервисов (Apache, PHP и MariaDB) в одном контейнере, что нарушает принцип «один сервис — один контейнер». Также образ получается тяжёлым, сложным в сопровождении и менее безопасным и масштабируемым.