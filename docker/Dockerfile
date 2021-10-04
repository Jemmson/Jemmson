FROM php:7.3.5-apache-stretch

ENV PATH="./vendor/bin:$PATH"
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=0
ENV PHP_OPCACHE_MAX_ACCELERATED_FILES=8000
ENV PHP_OPCACHE_MEMORY_CONSUMPTION=128

COPY . /var/www/html
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/config/php/conf.d/*.ini /usr/local/etc/php/conf.d/
COPY docker/start.sh /usr/local/bin/start
COPY docker/config/php/php.ini /usr/local/etc/php/php.ini

RUN chown -R www-data:www-data /var/www/html
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install bcmath
RUN apt-get update
RUN apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
RUN docker-php-ext-install -j$(nproc) mbstring exif gd
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug opcache
RUN a2enmod rewrite negotiation
RUN chmod +x /usr/local/bin/start

CMD ["/usr/local/bin/start"]
