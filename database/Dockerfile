FROM alpine:3.24.1

RUN apk add --no-cache mariadb mariadb-client

ARG DB_USERNAME

RUN addgroup -g 1001 -S mariadb && adduser -S $DB_USERNAME -u 1001 -G mariadb

# copy the current working directory into a directory
RUN mkdir -p /usr/entry

COPY . /usr/entry

EXPOSE 3306

COPY entry.sh /usr/local/bin

RUN chmod +x /usr/local/bin/entry.sh

# Windows CRLF messes up the script
RUN dos2unix "/usr/local/bin/entry.sh"

ENTRYPOINT ["/bin/sh", "/usr/local/bin/entry.sh"]
