FROM dunglas/frankenphp:latest

# Ustaw katalog roboczy
WORKDIR /app

# Skopiuj pliki aplikacji
COPY . /app/public

# Zainstaluj rozszerzenia PHP potrzebne dla Symfony
RUN install-php-extensions \
    gd \
    intl \
    zip \
    opcache \
    pdo_pgsql \
    pgsql

# 🧩 Zainstaluj Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Udostępnij porty HTTP/HTTPS
EXPOSE 80 443

# Domyślne polecenie uruchomienia FrankenPHP
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
