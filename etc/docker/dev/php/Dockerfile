FROM php:7.2-cli

RUN echo "\nexport TERM=xterm" >> /etc/bash.bashrc \
 && apt-get update && apt-get install -y --no-install-recommends \
    apt-utils apt-transport-https

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    git \
    libzip-dev \
    zip \
    unzip \
     && rm -rf /var/lib/apt/lists/*

# Xdebug install
RUN pecl install zip \
    && docker-php-ext-enable zip

# Composer and no dev vendor requirements
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

WORKDIR /app