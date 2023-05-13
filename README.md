# Promo code

## Installation

```bash
$ git clone git@github.com:AmineAlyate/promo-code.git
```
> Inside the project folder, run the following:

```bash
$ docker-compose  up -d
$ docker-compose start
```

### Database
Connect to the mysql container and run the following commands:

```bash
$ docker exec -it promo-code-mysql-1 bash
$ mysql -u root -ppassword
$ create database promo_code character set UTF8mb4 collate utf8mb4_bin;
```

Then connect to the php container and run the following commands:

```bash
$ docker exec -it promo-code-laravel.test-1 bash
$ composer install
$ php artisan migrate
$ php artisan db:seed
```

On your vhosts file, add the following:

```bash
127.0.0.1 promo-code.test
```

## Postman collection

You can find the postman collection in the root folder of the project or check the following link:

https://texa-team.postman.co/workspace/Promo-Code~410a3ca4-077b-41fd-a592-b930bfcd26b7/collection/24151818-e0df1c25-8b9a-4c00-9f7a-6093752bbeaa?action=share&creator=24151818

Make sure to select the environment "Dev env" before running the requests.

Available endpoints:

| Admin                      |                                                           User                                                           |                                  Guest                                       |
|:---------------------------|:------------------------------------------------------------------------------------------------------------------------:|-----------------------------------------------------------------------------:|
| Create promo code<br/>`/api/promo-codes` | Validate promo code<br/>`/api//promo-codes/validate`<br/>This endpoint requires two params<br/> promo code and the amont | Login as a user `/api/users/login`<br/>Login as an admin `/api/admins/login` |

