# Installation

1. Install [docker](https://docs.docker.com/) and [docker-compose](https://docs.docker.com/compose/install/)
2. Run `docker-compose up` in the root directory
3. While the server is running, execute `docker exec -t website_web_1 bash -c "mysql -h db -u root -p1234 erettsegik < schema.sql"` to initialize the database

# Usage

 - To start the webserver at any time, run `docker-compose up`
 - To access the database, run `mysql -u root -p -h 127.0.0.1`
