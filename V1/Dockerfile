# Usar imagem oficial do PHP 7.2 com Apache
FROM php:7.2-apache

# Instalar dependências e extensões necessárias
RUN apt-get update && apt-get install -y \
    libonig-dev \
    && docker-php-ext-install pdo_mysql \
    && a2enmod rewrite  # Habilitar mod_rewrite para .htaccess

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

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

# Expor a porta 80
EXPOSE 80