FROM php:7.2.34-fpm
MAINTAINER mf1969@gmail.com mafio

ENV   DEBIAN_FRONTEND=noninteractive \
      APP_ENV=${APP_ENV:-prod} \
      DISPLAY_ERROR=${DISPLAY_ERROR:-off} \
      XDEBUG_MODE=${XDEBUG_MODE:-off} \
      PHP_DATE_TIMEZONE=${PHP_DATE_TIMEZONE:-Europe/Warsaw} \
      SUPERCRONIC_URL=https://github.com/aptible/supercronic/releases/download/v0.1.12/supercronic-linux-amd64 \
      SUPERCRONIC=supercronic-linux-amd64 \
      SUPERCRONIC_SHA1SUM=048b95b48b708983effb2e5c935a1ef8483d9e3e

RUN apt update && apt upgrade -y && apt install -y apt-utils \
    && apt install -y lsb-release ca-certificates apt-transport-https software-properties-common \
    && apt install -y wget curl cron git unzip gnupg2 build-essential && apt install -y nginx \
    && apt-get install -y libmcrypt-dev\
    && pecl install mcrypt-1.0.0 \
    && apt install -y libicu-dev && apt-get install g++ && rm -rf /tmp/pear \
    && apt -y full-upgrade && apt -y autoremove && ln -s /var/log/nginx/ `2>&1 nginx -V | grep -oP "(?<=--prefix=)\S+"`/logs \
    && apt-get update && apt-get install -y supervisor && mkdir -p /var/log/supervisor \
    && docker-php-source extract \
    && pecl install xdebug \
    && pecl install -o -f redis \
    && pecl install mcrypt-1.0.1 \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure mysqli --with-mysqli=mysqlnd \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable redis \
    && docker-php-source delete \
    &&  curl -fsSLO "$SUPERCRONIC_URL" \
        && echo "${SUPERCRONIC_SHA1SUM}  ${SUPERCRONIC}" | sha1sum -c - \
        && chmod +x "$SUPERCRONIC" \
        && mv "$SUPERCRONIC" "/usr/local/bin/${SUPERCRONIC}" \
        && ln -s "/usr/local/bin/${SUPERCRONIC}" /usr/local/bin/supercronic


WORKDIR /

COPY config/custom.ini /usr/local/etc/php/conf.d/custom.ini
COPY config/dockerFile/nginx.conf /etc/nginx/nginx.conf
COPY config/dockerFile/mime.types /etc/nginx/mime.types
COPY config/dockerFile/enabled-app.conf /etc/nginx/conf.d/enabled-app.conf
COPY config/supervisord-main.conf /etc/supervisord.conf
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY config/cron-task /etc/cron.d/crontask

RUN  rm -Rf /var/www/*
COPY public/ /main/public
WORKDIR /main

STOPSIGNAL SIGQUIT
EXPOSE 8000

CMD ["/usr/bin/supervisord", "-nc", "/etc/supervisord.conf"]