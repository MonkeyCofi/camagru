FROM nginx:alpine3.24

EXPOSE 80

# RUN apk add --no-cache shadow && \
#     usermod -u $(id -u) nginx && \
#     groupmod -g $(id -g) nginx

RUN sed -i 's/user  nginx;/user  root;/g' /etc/nginx/nginx.conf

COPY default.conf /etc/nginx/conf.d

CMD ["nginx", "-g", "daemon off;"]
