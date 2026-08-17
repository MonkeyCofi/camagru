FROM alpine:3.24.1

RUN apk add --no-cache mariadb mariadb-client

# copy the current working directory into a directory
RUN mkdir -p /usr/entry

COPY . /usr/entry

EXPOSE 3306

COPY entry.sh /usr/local/bin
RUN chmod +x /usr/local/bin/entry.sh

ENTRYPOINT ["/bin/sh", "/usr/local/bin/entry.sh"]
