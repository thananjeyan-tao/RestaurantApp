# RestaurantApp
A Backend Service for Managing Restaurant Orders with Kitchen Capacity throttling, Complete Order, Active orders List, VIP Priority etc.. Built with PHP + Laravel and uses MySQL for persistence.

## Features
* Create orders with capacity throttling (max N active orders).
* VIP orders bypass kitchen limits.
* View all active orders.
* Complete orders to free capacity.
* Orders are persisted in DB (MySQL).
* VIP priority queue
* Suggests next available ordering time when full.
* Auto-complete after X seconds
* Unit and Feature Tests
* Capacity and completion time configurable via `config/kitchen.php`


## Setup Local Development
Recommend to use docker. Don't worry with the help of [Laravel Sail](https://laravel.com/docs/master/sail), very little knowledge of docker is required.

First you need to install docker in your local machine. [instruction](https://docs.docker.com/get-docker/)

After successfully install docker the rest will be easy. <br>
*Note: The instruction will be base on Ubuntu but won't be so much different from other OS*

### Install the app
Clone the repo, navigate into the folder, and open it in terminal:
```
git clone https://github.com/thananjeyan-tao/RestaurantApp.git
cd RestaurantApp
```

copy `.env.example` to `.env` and update accordingly
```
cp .env.example .env
```

Install  Dependencies
```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

create container (this will take times)
```
sail up -d
```
migrate database
```
sail artisan migrate
```

run backround job for auto complete order
```
sail artisan queue:work
```
running tests
```
sail artisan test
```

### API Endpoints
Base URL: http://localhost/api/

* Create Order POST /orders
* List Active Orders GET /orders/active
* Complete Order POST /orders/{id}/complete
* Priority Queue (VIP first) GET /orders/priority

#### Default Port
* App: 80
* Database: 3306

## Tech Stack
* PHP 8.4
* MySQL 8.0

## Support

If you encounter any issues or need more details, please feel free to reach out to me. Thank you!
