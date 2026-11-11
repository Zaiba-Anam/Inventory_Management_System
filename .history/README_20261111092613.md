# Inventory Management System

A web-based admin panel built with Laravel for managing inventory, purchases, sales, and warehouses.

## Features

- User authentication and profile management
- Role and permission management
- Products, categories, and brands
- Suppliers, customers, and warehouses
- Purchases, sales, and returns
- Stock transfers between warehouses
- PDF invoices
- Reports with date filters

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL (or another database supported by Laravel)

## Installation

```bash
# Clone the repository
git clone <your-repo-url>
cd <project-folder>

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate
```

Update the database settings in `.env`, then run:

```bash
php artisan migrate
npm run build
php artisan serve
```

The app will be available at `http://localhost:8000`.

## Usage

1. Register or log in.
2. Add your warehouses, suppliers, customers, brands, and categories.
3. Add products.
4. Record purchases and sales to update stock.
5. View reports from the Reports menu.

## Tech Stack

- Laravel
- Laravel Breeze
- Spatie Laravel Permission
- Intervention Image
- Laravel DomPDF
- Bootstrap 5

## Contributing

Contributions are welcome. Please fork the repository and open a pull request.

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
