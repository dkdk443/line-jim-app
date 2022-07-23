FROM node:16.15.0-alpine
WORKDIR /var/www/html
RUN apk add bash
RUN apk add curl