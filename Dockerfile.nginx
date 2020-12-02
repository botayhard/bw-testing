FROM nginx:stable-alpine

RUN  echo "daemon off;" >> /etc/nginx/nginx.conf
ADD ./nginx/default.conf /etc/nginx/conf.d/default.conf

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

CMD ["nginx"]
