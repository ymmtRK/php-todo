FROM mysql:8.0

ENV TZ=UTC \
    MYSQL_DATABASE=todo \
    MYSQL_USER=mtkr3 \
    MYSQL_PASSWORD=ymmt1026 \
    MYSQL_ROOT_PASSWORD=root

COPY ./my.cnf /etc/mysql/conf.d/my.cnf
RUN chmod 644 /etc/mysql/conf.d/my.cnf