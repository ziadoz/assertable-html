FROM php:8.4.0beta4-fpm-bookworm
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN apt-get update && \
    apt-get install -y git zip p7zip-full && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

