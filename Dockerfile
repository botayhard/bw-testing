FROM node:15.3.0-buster

WORKDIR /app
COPY ./admin-master/package.json package.json
#COPY ./admin-master/package-lock.json package-lock.json
RUN npm install
COPY ./admin-master .
CMD npm run install-start
