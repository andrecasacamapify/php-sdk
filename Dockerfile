FROM php:${PHP_VERSION:-5.6}-cli-alpine

RUN apk add --update \
    curl

ADD . /sdk
WORKDIR /sdk

RUN php composer.phar install

CMD [ "sh", "-c", "php composer.phar run test" ]