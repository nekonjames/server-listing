FROM php:8.1-fpm

# Install additional tools
RUN rm /etc/apt/preferences.d/no-debian-php && \
    apt-get update && apt-get install -y \
    openssl g++ libzip-dev git zlib1g-dev libicu-dev libcurl3-dev zip unzip libpq-dev libonig-dev \
    libxml2-dev libxslt1-dev libpng-dev

# Install php plugins
RUN docker-php-ext-configure intl \
	&& docker-php-ext-install \
		mbstring pdo pdo_pgsql zip opcache curl intl xsl

RUN docker-php-ext-configure gd \
    && docker-php-ext-install gd \
    && docker-php-ext-enable gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Install XDebug (easy debugging & code coverage)
RUN if [ ! "$LIVE" ]; then yes | pecl install xdebug-3.1.2 && docker-php-ext-enable xdebug; fi

# Add config files
COPY php.ini /usr/local/etc/php/php.ini
COPY www.conf /usr/local/etc/php-fpm.d/www.conf

COPY xdebug.ini /tmp/php-dev-ext/docker-php-ext-xdebug.ini

RUN if [ ! "$LIVE" ]; then cp /tmp/php-dev-ext/* /usr/local/etc/php/conf.d/; fi

# Setup workdir and user
WORKDIR /var/www/app
RUN chown -R www-data:www-data /var/www

# Add access for default system user, so there's no issues with file permissions
ARG PUID
RUN if [ "$PUID" ]; then usermod -u $PUID www-data; fi
USER www-data
