# BiteRoute — Online Food Delivery System

BiteRoute is a Laravel 12 web application for managing customers, restaurants, restaurant menus, orders, and deliveries. It uses five related application tables. The requested Food and Items concepts share one `foods` catalog table.

## Requirements

- PHP 8.2 or newer with PDO SQLite enabled
- Composer

## Run locally

```bash
composer install
copy .env.example .env
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --seed
php artisan serve
```

Open the URL shown by `artisan serve` (usually `http://127.0.0.1:8000`). The default environment uses SQLite at `database/database.sqlite`. Sessions and cache use local files, and the queue is synchronous, so the application does not need infrastructure tables beyond its five domain tables. The `composer setup` script also creates the SQLite file when needed.

To rebuild the database and sample records:

```bash
php artisan migrate:fresh --seed
```

## Database relationships

1. `customers` — one customer has many orders.
2. `restaurants` — one restaurant has many foods/menu items.
3. `foods` — each food belongs to a restaurant and can be referenced by many orders.
4. `orders` — each order belongs to a customer and a food; the total is calculated from the current food price and quantity.
5. `deliveries` — an order has at most one delivery; deleting an order also removes its delivery.

Foreign keys protect referenced customers, restaurants, foods, and orders. The interface prevents deleting records that are still referenced.

## Features

- Dashboard with record counts and recent orders
- Create, list, view, edit, and delete screens for all five resources
- Server-side validation, inline feedback, duplicate email/order protection, and delete confirmations
- Eloquent relationships and eager-loaded related data
- Model factories and a seeder that creates usable related sample data
- Responsive Bootstrap 5 interface loaded from the CDN

## GitHub submission

Commit the source, migrations, factories, and seeders. Do not upload `.env`; it is excluded from version control. Use `.env.example` as the safe local configuration template, and submit the GitHub repository URL separately through CASTLY.

## Tests

```bash
php artisan test
```
