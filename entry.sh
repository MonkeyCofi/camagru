#!/bin/sh

# Ensure the data directory exists and is owned by the mysql user
if [ ! -d "/var/lib/mysql/mysql" ]; then
    echo "Initializing database..."
    mariadb-install-db --user=mysql --datadir=/var/lib/mysql > /dev/null
    
    # Configure default security/passwords if requested
    if [ ! -z "$MYSQL_ROOT_PASSWORD" ]; then
        tfile=`mktemp`
        if [ ! -f "$tfile" ]; then
            return 1
        fi
        cat << EOF > $tfile
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY '$MYSQL_ROOT_PASSWORD';
EOF
        if [ ! -z "$MYSQL_DATABASE" ]; then
            echo "CREATE DATABASE IF NOT EXISTS \`$MYSQL_DATABASE\` ;" >> $tfile
        fi
        mariadbd --user=mysql --bootstrap < $tfile
        rm -f $tfile
    fi
fi

echo "Starting MariaDB server..."
exec mariadbd --user=mysql --console --skip-networking=0