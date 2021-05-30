# Installation

1. Create a `config.php` file in the root directory with the database configuration:

```php
<?php

$config['db'] = array(
    'host'     => "localhost",
    'username' => "newuser",
    'password' => "password",
    'dbname'   => "erettsegik",
    'charset'  => 'utf8'
  );
```

For creating users in MySQL, see [this link](https://www.digitalocean.com/community/tutorials/how-to-create-a-new-user-and-grant-permissions-in-mysql).

2. Create the database

```
mysql> create database erettsegik;
```

```
$ mysql -u newuser -p erettsegik < schema.sql
```

3. Install composer dependencies

```
$ composer install
```

4. Run the local PHP server

```
$ php -S localhost:8000
```
