FROM php:5.6-fpm
MAINTAINER mf1969@gmail.com mafio

WORKDIR /

ENV   DEBIAN_FRONTEND=noninteractive \
      APP_ENV=${APP_ENV:-prod} \
      DISPLAY_ERROR=${DISPLAY_ERROR:-off} \
      XDEBUG_MODE=${XDEBUG_MODE:-off} \
      PHP_DATE_TIMEZONE=${PHP_DATE_TIMEZONE:-Europe/Warsaw} \
      SUPERCRONIC_URL=https://github.com/aptible/supercronic/releases/download/v0.1.12/supercronic-linux-amd64 \
      SUPERCRONIC=supercronic-linux-amd64 \
      SUPERCRONIC_SHA1SUM=048b95b48b708983effb2e5c935a1ef8483d9e3e \
      DB_USERNAME=${DB_USERNAME} \
      DB_DATABASE=${DB_DATABASE} \
      DB_PASSWORD=${DB_PASSWORD} \
      DB_HOST=${DB_HOST}

RUN apt update && apt upgrade -y && apt install -y apt-utils \
    && apt install -y lsb-release ca-certificates apt-transport-https software-properties-common \
    && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && apt install -y zlib1g-dev  \
    && apt install -y wget curl cron git unzip gnupg2 build-essential && apt install -y nginx \
    && apt install -y libicu-dev && apt-get install g++ && rm -rf /tmp/pear \
    && apt -y full-upgrade && apt -y autoremove && ln -s /var/log/nginx/ `2>&1 nginx -V | grep -oP "(?<=--prefix=)\S+"`/logs \
    && apt install -y libc-client-dev libkrb5-dev && rm -r /var/lib/apt/ \
    && apt-get update && apt-get install -y libmcrypt-dev\
    && apt-get update && apt-get install -y supervisor && mkdir -p /var/log/supervisor \
    && mkdir -p /usr/local/jpg \
    && mkdir -p /usr/local/free \
    && apt-get -y install gcc make autoconf libc-dev pkg-config \
    && apt-get -y install -y libmcrypt-dev \
    && pecl channel-update pecl.php.net \
    && docker-php-source extract \
        && pecl install xdebug-2.5.5 \
        && pecl install redis-2.2.8 \
        && pecl install --nodeps mailparse-2.1.6 \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure mysqli --with-mysqli=mysqlnd \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-configure gd  \
        && docker-php-ext-install mbstring \
        && docker-php-ext-install mysqli \
        && docker-php-ext-install pdo_mysql \
        && docker-php-ext-install imap \
        && docker-php-ext-install gd \
        && docker-php-ext-install mcrypt \
        && docker-php-source delete \
    && curl -fsSLO "$SUPERCRONIC_URL" \
    && echo "${SUPERCRONIC_SHA1SUM}  ${SUPERCRONIC}" | sha1sum -c - \
    && chmod +x "$SUPERCRONIC" \
    && mv "$SUPERCRONIC" "/usr/local/bin/${SUPERCRONIC}" \
    && ln -s "/usr/local/bin/${SUPERCRONIC}" /usr/local/bin/supercronic \
    && curl -sS https://getcomposer.org/installer | php \
    && cp composer.phar /usr/local/bin/composer  \
    && mv composer.phar /usr/bin/composer

COPY ./main /main
COPY config/custom.ini /usr/local/etc/php/conf.d/custom.ini
COPY config/dockerFile/nginx.conf /etc/nginx/nginx.conf
COPY config/dockerFile/mime.types /etc/nginx/mime.types
COPY config/dockerFile/enabled-app.conf /etc/nginx/conf.d/enabled-app.conf
COPY config/supervisord-main.conf /etc/supervisord.conf
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY config/cron-task /etc/cron.d/crontask

WORKDIR /main

STOPSIGNAL SIGQUIT
EXPOSE 8080

CMD ["/usr/bin/supervisord", "-nc", "/etc/supervisord.conf"]
