#!/bin/sh

############# Create DB ###################
createDatabase() {

  if [ ! -f .env ]; then
    echo "INFO Create MySQL DB"

    dbUser='csp'
    # create random password
    dbPass="$(openssl rand -base64 12)"
    dbDatabase=${dbUser}_1
    dbHost='localhost'

    mysql -e "CREATE DATABASE ${dbDatabase} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" &&
    mysql -e "CREATE USER ${dbUser}@'${dbHost}' IDENTIFIED BY '${dbPass}';" &&
    mysql -e "GRANT ALL PRIVILEGES ON ${dbDatabase}.* TO '${dbUser}'@'${dbHost}';" &&
    mysql -e "FLUSH PRIVILEGES;" &&

    cat >.env <<EOL
DB_DB="${dbDatabase}"
DB_USER="${dbUser}"
DB_PASS="${dbPass}"
DB_HOST="${dbHost}"
EOL

    mv .env ../Private/;
  fi
}

createDatabase;
mysql -u ${dbUser} -p-password="${dbPass}" -h ${dbHost} ${dbDatabase} < db.sql

