## Atmiya API

## Setup

-   Clone repository https://github.com/krishnainti/atmiya-api
-   Update `.env` file with `.env.example`
-   Run the `composer install` to install the dependecy packages
-   Run the `php artisan migrate` to update the Database schema
-   Run the `php artisan serve` to serve the application in :8000 port

## Steps for updating backend changes

1. Upload Project Files
   Using the file manager upload the files which has changes or the files newly created to root directory (e.g., "https://php.atmiyausa.org/public_html").
2. Running migrations (if required)
   to run migration we need to run “php artisan migrate” commend in root Directory. Since we don’t have access to terminal we can you of from browser by visiting ‘https://php.atmiyausa.org/run-migration’ url this will create migration.

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
