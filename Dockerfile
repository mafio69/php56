FROM php:7.2.34-fpm
MAINTAINER mf1969@gmail.com mafio

ENV DEBIAN_FRONTEND=noninteractive \
      APP_ENV=${APP_ENV:-prod} \
      DISPLAY_ERROR=${DISPLAY_ERROR:-off} \
      XDEBUG_MODE=${XDEBUG_MODE:-off} \
      PHP_DATE_TIMEZONE=${PHP_DATE_TIMEZONE:-Europe/Warsaw}

RUN apt update && apt upgrade -y && apt install -y apt-utils \
    && apt install -y lsb-release ca-certificates apt-transport-https software-properties-common \
    && apt install -y wget curl cron git unzip gnupg2 build-essential && apt install -y nginx \
    && apt install -y libicu-dev && apt-get install g++ && rm -rf /tmp/pear \
    && apt -y full-upgrade && apt -y autoremove && ln -s /var/log/nginx/ `2>&1 nginx -V | grep -oP "(?<=--prefix=)\S+"`/logs \
    && apt-get update && apt-get install -y supervisor && mkdir -p /var/log/supervisor \
    && docker-php-source extract \
    && pecl install xdebug \
    && pecl install -o -f redis \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure mysqli --with-mysqli=mysqlnd \
    && docker-php-ext-configure sockets \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install sockets \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-enable redis \
    && docker-php-ext-enable intl \
    && docker-php-ext-enable sockets \
    && docker-php-source delete

WORKDIR /

COPY config/custom.ini /usr/local/etc/php/conf.d/custom.ini
COPY config/dockerFile/nginx.conf /etc/nginx/nginx.conf
COPY config/dockerFile/mime.types /etc/nginx/mime.types
COPY config/dockerFile/enabled-app.conf /etc/nginx/conf.d/enabled-app.conf
COPY config/supervisord-main.conf /etc/supervisord.conf
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN  rm -Rf /var/www/*
COPY public/ /main/public
STOPSIGNAL SIGQUIT
EXPOSE 8000

CMD ["/usr/bin/supervisord", "-nc", "/etc/supervisord.conf"]