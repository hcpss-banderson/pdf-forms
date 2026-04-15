FROM php:8.4-apache

ENV HTML_EXTERNAL_ENDPOINT=http://localhost:4321
ENV HTML_INTERNAL_ENDPOINT=http://astro:4321
ENV PDF_REACTOR_ENDPOINT=http://pdfreactor:9423/service/rest

COPY ./index.php /var/www/html/index.php
COPY ./PDFreactor.class.php /var/www/PDFreactor.class.php
