# =============================================================================
# Dockerfile API — FrankenPHP + Symfony 8
# =============================================================================
# Image officielle FrankenPHP qui embarque PHP 8.4 + Caddy
# C'est un serveur d'applications PHP tout-en-un

FROM dunglas/frankenphp:1.11.1-php8.4-bookworm

# --------------------------------------------------------------------------
# 1. Extensions PHP nécessaires pour Symfony + Doctrine + PostgreSQL
# --------------------------------------------------------------------------
# install-php-extensions est un script intégré à l'image FrankenPHP
# qui simplifie énormément l'installation des extensions PHP
RUN install-php-extensions \
    pdo_pgsql \
    intl \
    opcache \
    zip \
    apcu

# --------------------------------------------------------------------------
# 2. Composer (gestionnaire de dépendances PHP)
# --------------------------------------------------------------------------
# On copie le binaire depuis l'image officielle Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --------------------------------------------------------------------------
# 3. Configuration PHP pour le développement
# --------------------------------------------------------------------------
# En dev on veut : affichage des erreurs, pas d'OPcache (sinon les modifs
# ne sont pas prises en compte sans restart)
RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

# --------------------------------------------------------------------------
# 4. Répertoire de travail
# --------------------------------------------------------------------------
# FrankenPHP sert par défaut depuis /app/public (convention Symfony)
WORKDIR /app

# --------------------------------------------------------------------------
# 5. Exposer le port
# --------------------------------------------------------------------------
# FrankenPHP écoute sur le port 80 par défaut en mode HTTP
# (pas HTTPS en dev pour simplifier)
EXPOSE 80