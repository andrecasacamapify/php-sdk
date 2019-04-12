ARG BASE_IMAGE=php:5.6-cli-alpine
FROM $BASE_IMAGE

RUN apk add --update \
    curl

ADD . /sdk
WORKDIR /sdk

RUN php composer.phar install

CMD [ "sh", "-c", "php composer.phar run test" ]