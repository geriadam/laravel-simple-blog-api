FROM php:7.3-fpm AS base

# Install dependencies
RUN apt-get update -q -y && apt-get install -q -y --no-install-recommends \
    build-essential \
    libmemcached-dev \
    libmcrypt-dev \
    libreadline-dev \
    libgmp-dev \
    libzip-dev \
    libz-dev \
    libzip-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libssl-dev \
    openssh-server \
    libmagickwand-dev \
    git \
    cron \
    nano \
    libxml2-dev \
    --assume-yes

# Install soap extention
RUN docker-php-ext-install soap

# Install for image manipulation
RUN docker-php-ext-install exif

# Install the PHP pcntl extention
RUN docker-php-ext-install pcntl

# Install the PHP intl extention
RUN docker-php-ext-install intl

# Install the PHP gmp extention
RUN docker-php-ext-install gmp

# Install the PHP zip extention
RUN docker-php-ext-install zip

# Install the PHP pdo_mysql extention
RUN docker-php-ext-install pdo_mysql

# Install the PHP pdo_pgsql extention
RUN docker-php-ext-install pdo_pgsql

# Install the PHP bcmath extension
RUN docker-php-ext-install bcmath

# Install Imagick
RUN pecl install imagick && \
    docker-php-ext-enable imagick

# Install the PHP gd library
RUN docker-php-ext-install gd && \
    docker-php-ext-configure gd \
        --with-jpeg-dir=/usr/lib \
        --with-freetype-dir=/usr/include/freetype2 && \
    docker-php-ext-install gd

# Install the php memcached extension
RUN pecl install memcached && docker-php-ext-enable memcached

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

FROM base AS dev

# Install composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
RUN composer config -g repo.packagist composer https://packagist.org

# # Install composer and add its bin to the PATH.
# RUN curl -s http://getcomposer.org/installer | php && \
#     echo "export PATH=${PATH}:/var/www/vendor/bin" >> ~/.bashrc && \
#     mv composer.phar /usr/local/bin/composer
# # Source the bash
# RUN . ~/.bashrc

FROM dev as production

# Set working directory
WORKDIR /var/www
RUN chown www:www -R .

# Change current user to www
USER www

# Copy composer.lock and composer.json
COPY --chown=www:www composer.lock composer.json ./
RUN composer install --no-dev --no-scripts --no-autoloader

# Copy existing application directory contents
COPY --chown=www:www . /var/www

RUN composer install

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
