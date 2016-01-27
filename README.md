1. Install docker and docker-compose
2. Run `docker-compose up` in the root directory
3. While the server is running, execute `docker exec -t website_web_1 bash -c "mysql -h db -u root -p1234 erettsegik < schema.sql"` to initialize the database
