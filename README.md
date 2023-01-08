# OpenTask

# Installation 

1. Clone Project
````sh
git clone https://github.com/Raldi4859/opentask.git
````
2. Composer install
````sh
composer install
````
3. import opentask.sql to your own mySQL database
4. Add Database Credential like yours in `.env`
```php
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=opentask
DB_USERNAME=root
DB_PASSWORD=
```
5. Go to the project - 
```sh
cd opentask
```
6. Run Project inside that directory - 
````sh
php artisan serve
````
7. Open in Browser 
````sh
http://localhost:8000
````
