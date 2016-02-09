# Requirements

 - [docker](https://docs.docker.com/) and [docker-compose](https://docs.docker.com/compose/install/)
 - A MySQL client (`sudo apt-get install mysql-client`)

# Installation

1. Run `docker-compose up` in the root directory
2. When the server is running, execute `docker exec -t erettsegikhu_web_1 bash -c "mysql -h db -u root -p1234 erettsegik < schema.sql"` to initialize the database
3. (optional) Also execute `docker exec -t erettsegikhu_web_1 bash -c "mysql -h db -u root -p1234 erettsegik < seed.sql"` to load data for development. Log in with `hello:asd`.

# Usage

 - To start the webserver at any time, run `docker-compose up`
 - To access the database, run `mysql -u root -p -h 127.0.0.1`
