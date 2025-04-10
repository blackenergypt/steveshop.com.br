# Usar imagem oficial do PHP 8.3 com Apache
FROM php:8.3-apache

# Instalar dependências e extensões necessárias
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install zip \
    && a2enmod rewrite \
    && a2enmod headers \
    && a2enmod expires \
    && a2enmod deflate \
    && a2enmod ssl  # Ativar módulo SSL

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar extensões adicionais necessárias para PHP 8.3
RUN pecl install redis \
    && docker-php-ext-enable redis

# Otimizações para produção
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do projeto para o contêiner
COPY . /var/www/html/

# Instalar dependências do Composer (se houver composer.json)
RUN if [ -f composer.json ]; then composer install; fi

# Configurar permissões para a pasta cdn e outros arquivos
RUN mkdir -p /var/www/html/cdn && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Configurar VirtualHost para HTTP (porta 80)
RUN echo '<VirtualHost *:80>\n\
    ServerAdmin webmaster@steveshop.com.br\n\
    ServerName steveshop.com.br\n\
    ServerAlias *.steveshop.com.br\n\
    DocumentRoot /var/www/html\n\
    \n\
    <Directory /var/www/html>\n\
        Options Indexes FollowSymLinks MultiViews\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    \n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/steveshop.conf

# Configurar VirtualHost para HTTPS (porta 443) usando os certificados da pasta ssl/certs
RUN echo '<VirtualHost *:443>\n\
    ServerAdmin webmaster@steveshop.com.br\n\
    ServerName steveshop.com.br\n\
    ServerAlias *.steveshop.com.br\n\
    DocumentRoot /var/www/html\n\
    \n\
    <Directory /var/www/html>\n\
        Options Indexes FollowSymLinks MultiViews\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    \n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
    \n\
    SSLEngine on\n\
    SSLCertificateFile /etc/ssl/certs/steveshop.crt\n\
    SSLCertificateKeyFile /etc/ssl/certs/steveshop.key\n\
</VirtualHost>' > /etc/apache2/sites-available/steveshop-ssl.conf

# Ativar os VirtualHosts
RUN a2ensite steveshop.conf && \
    a2ensite steveshop-ssl.conf && \
    a2dissite 000-default.conf

# Expor as portas 80 e 443
EXPOSE 80 443