FROM mysql:5.7


VOLUME /var/lib/mysql
#Luego se ejecutaran por orden alfabetico
COPY ./script/1crearBBDD.sql /docker-entrypoint-initdb.d/

EXPOSE 3306