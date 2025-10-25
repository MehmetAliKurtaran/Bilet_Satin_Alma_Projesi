# /bilet-satin-alma/Dockerfile

# Apache ve PHP 8.2 için resmi imajı kullan
FROM php:8.2-apache

# Gerekli sistem kütüphanelerini (libsqlite3-dev) kur,
# ardından PHP eklentilerini (pdo_sqlite) kur ve son olarak apt önbelleğini temizle.
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

# Apache URL yeniden yazma modülünü (mod_rewrite) etkinleştir
RUN a2enmod rewrite

# Proje dosyalarını web sunucusunun kök dizinine kopyala
COPY . /var/www/html/

# /public klasörünü Apache'nin ana dizini olarak ayarla
RUN sed -i -e 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# /database klasörünün Apache tarafından yazılabilir olmasını sağla
RUN chown -R www-data:www-data /var/www/html/database
RUN chmod -R 775 /var/www/html/database

# Konteyner başladığında Apache'yi ön planda çalıştır
CMD ["apache2-foreground"]