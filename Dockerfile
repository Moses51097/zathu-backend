FROM php:8.2-cli
WORKDIR /app
COPY . /app
CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]0
FROM php:8.2-cli
WORKDIR /app
COPY . /app
CMD ["php", "-S", "0.0.0.0:10000", "index.php"]0
