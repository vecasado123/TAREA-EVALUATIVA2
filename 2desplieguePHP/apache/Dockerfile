FROM ubuntu:18.04

RUN apt-get update && apt-get install -y apache2 \
    && apt-get -y install libapache2-mod-fcgid \
    && a2enmod proxy_fcgi 
    

COPY 000-default.conf /etc/apache2/sites-available

COPY index.php /var/www/html
COPY dwes.css /var/www/html

EXPOSE 80
CMD ["apachectl", "-D", "FOREGROUND"]