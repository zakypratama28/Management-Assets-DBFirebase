# Menggunakan image PHP versi 8.2 dengan web server Apache
FROM php:8.2-apache

# Mengaktifkan mod_rewrite (berguna jika Anda memakai routing/htaccess nantinya)
RUN a2enmod rewrite

# Menyalin seluruh kode dari repositori Anda ke dalam folder public HTML Apache
COPY . /var/www/html/

# Menyesuaikan hak akses agar Apache dapat membaca direktori
RUN chown -R www-data:www-data /var/www/html/

# Membuka port 80 untuk lalu lintas web
EXPOSE 80
