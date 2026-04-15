# create from debian image
FROM debian:latest

# mount volume for mysql data
VOLUME /var/lib/mysql

# mount volume for logs
VOLUME /var/log

# install apache2, php, mod_php for apache2, php-mysql and mariadb, supervisor
RUN apt-get update && \
    apt-get install -y supervisor && \
    apt-get install -y apache2 php libapache2-mod-php php-mysql mariadb-server && \
    apt-get clean

# add wordpress files to /var/www/html
# 1. Download the archive to a temporary directory
ADD https://wordpress.org/latest.tar.gz /tmp/wordpress.tar.gz

# 2. Extract wordpress directly into /var/www/html/ and clean up
RUN tar -xzf /tmp/wordpress.tar.gz -C /var/www/html/ && \
    rm /tmp/wordpress.tar.gz && \
    chown -R www-data:www-data /var/www/html/wordpress


# copy the configuration file for apache2 from files/ directory
COPY files/apache2/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY files/apache2/apache2.conf /etc/apache2/apache2.conf

# copy the configuration file for php from files/ directory
COPY files/php/php.ini /etc/php/8.4/apache2/php.ini

# copy the configuration file for mysql from files/ directory
COPY files/mariadb/50-server.cnf /etc/mysql/mariadb.conf.d/50-server.cnf

# copy the supervisor configuration file
COPY files/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# copy the configuration file for wordpress from files/ directory
COPY files/wp-config.php /var/www/html/wordpress/wp-config.php

# create mysql socket directory
RUN mkdir /var/run/mysqld && chown mysql:mysql /var/run/mysqld

# open port 80
EXPOSE 80

# start supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]

RUN mkdir -p /var/log/mysql && \
    mkdir -p /var/lib/mysql && \
    chown -R mysql:mysql /var/log/mysql /var/lib/mysql