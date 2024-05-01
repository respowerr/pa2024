FROM php:8.0-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /web/

COPY . /var/www/html/

RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html|' /etc/apache2/sites-available/000-default.conf

RUN echo '<Directory "/var/www/html/public">\n    DirectoryIndex index.php\n</Directory>' >> /etc/apache2/apache2.conf

EXPOSE 80