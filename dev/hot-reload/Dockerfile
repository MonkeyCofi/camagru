FROM node:alpine

# turn the current directory into a volume so that the node_modules install and stay in the volume
RUN mkdir -p /usr/local/app

COPY . /usr/local/app

WORKDIR /usr/local/app

RUN npm i

CMD ["node", "server.js"]