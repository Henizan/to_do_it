FROM php:8.2-apache

# Installer les dépendances système pour PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP pdo et pdo_pgsql
RUN docker-php-ext-install pdo pdo_pgsql

# Activer le mod_rewrite d'Apache (utile pour de futures URLs propres)
RUN a2enmod rewrite

# Copier le code local vers le répertoire de travail du conteneur
COPY . /var/www/html/

# Donner les droits appropriés à Apache
RUN chown -R www-data:www-data /var/www/html
