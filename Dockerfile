FROM php:7.2-cli
RUN apt-get update -qq && docker-php-ext-install pdo_mysql
COPY . /app/MyNotes
WORKDIR /app/MyNotes
CMD ["php", "-S", "0.0.0.0:80"]
