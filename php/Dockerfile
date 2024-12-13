FROM php:8.3-apache

# 必要なパッケージをインストール
RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    git \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# php.iniとApacheの設定ファイルをコピー
COPY ./config/php/php.ini /usr/local/etc/php/
COPY ./config/apache2/apache2.conf /etc/apache2/

# phpフォルダの中身を /var/www/html にコピー
COPY ./www /var/www/html

# ポートの公開（情報として書くだけ）
EXPOSE 80

# ApacheのデフォルトCMD
CMD ["apache2-foreground"]
