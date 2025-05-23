FROM nginx

# RUN sudo apt-get install nginx-extras

RUN mkdir -p /var/www/public

COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

WORKDIR /var/www/public
