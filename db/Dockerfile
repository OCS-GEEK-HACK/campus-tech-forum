FROM postgres:13

# pgcrypto拡張のインストール
RUN apt-get update && \
    apt-get install -y postgresql-contrib && \
    rm -rf /var/lib/apt/lists/*

# データベース起動時にpgcryptoを有効にする
RUN echo "shared_preload_libraries = 'pgcrypto'" >> /usr/share/postgresql/postgresql.conf.sample
