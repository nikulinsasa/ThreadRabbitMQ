FROM php:7.2-rc-zts-alpine

ARG APP_USER_USERNAME=app

RUN apk update && apk add --no-cache \
    sudo bash \
    g++ make autoconf \
    libxml2-dev icu-dev curl-dev pcre-dev

RUN adduser -D -s /bin/bash $APP_USER_USERNAME \
    && addgroup $APP_USER_USERNAME wheel \
    && echo "$APP_USER_USERNAME:" | chpasswd \
    && echo -e "# User rules for $APP_USER_USERNAME\n$APP_USER_USERNAME ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/docker-init

RUN docker-php-ext-install curl
RUN docker-php-ext-install opcache
RUN docker-php-ext-install pcntl
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install bcmath

RUN curl -sSL https://github.com/krakjoe/pthreads/archive/master.zip -o /tmp/pthreads.zip \
    && unzip /tmp/pthreads.zip -d /tmp \
    && cd /tmp/pthreads-* \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && rm -rf /tmp/pthreads*

RUN docker-php-ext-enable pthreads

RUN curl -sSL https://github.com/xdebug/xdebug/archive/4ada850.zip -o /tmp/xdebug.zip \
    && unzip /tmp/xdebug.zip -d /tmp \
    && cd /tmp/xdebug-* \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && rm -rf /tmp/xdebug*

RUN docker-php-ext-enable xdebug \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_host=docker.for.mac.localhost" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mv /etc/profile.d/color_prompt /etc/profile.d/color_prompt.sh \
    && echo -e "alias egrep='egrep --color=auto'\n\
alias l='ls -CF'\n\
alias la='ls -A'\n\
alias ll='ls -alF'\n\
alias ls='ls --color=auto'" >> /etc/profile.d/aliases.sh


COPY . /usr/phpreact
CMD [ "php", "main.php" ]