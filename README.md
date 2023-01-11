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
4. Add Database Credential and Email Credential like yours in `.env`
```php
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=opentask
DB_USERNAME=root
DB_PASSWORD=

MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email_username
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME=OpenTask
```
Note: If you failed login to your email access: https://security.google.com/settings/security/apppasswords and choose generate a 'Email' Password and set device as Custom (This setting only exist if two-factor authentication enabled on your Google account. and copy password generated to MAIL_PASSWORD

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
8. Register Account using a different active account from account that used in .env because the notification send directly via email
