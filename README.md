# Check Migrations

A Laravel command scans your application for pending migrations, providing a clear overview of what needs to be migrated.
You can then choose to run individual migrations or skip them as needed, short-cutting and optimizing your development workflow.

## Support for Structured Migrations

This package seamlessly handles migrations organized within subdirectories, ensuring that no pending migrations are overlooked regardless of the project's migration structure. 

```php
database
└── migrations
├── 2023_01_01
│ ├── 20230101000001_create_table_one.php
│ └── 20230101000002_create_table_two.php
└── 2023_02_01
├── 20230201000001_create_table_three.php
└── 20230201000002_create_table_four.php

```
## Installation

```bash
composer require levizoesch/laravel-check-migrations
```

## Usage

Run the following command in your terminal:

#### This will display pending migrations and prompt for confirmation before running each one.
```bash
php artisan check-migrations
```
#### This will skip confirmation and run pending migrations directly.
```bash
php artisan check-migrations --skip
```
#### This will ignore the migration named 20230101000000_create_example_table from running and prompting for confirmation.
```bash
php artisan check-migrations --ignore=20230101000000_create_example_table
```

### Notes

Ensure that your Laravel project is properly configured and migrations are set up correctly for this command to work effectively.

Always review pending migrations before running them in production environments to prevent unintended consequences.