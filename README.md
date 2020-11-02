### Requirements:
    - PHP >= 7.4
    - Composer

### Installation & Usage
    - run `composer install`
    - for testing: run `vendor/bin/phpunit`

### How the project is structured?
    - The core part of the project is the `config/data-providers.php` file, it's where you can add/remove/enable/disable payment providers.
    - To add a new provider just add a new entry to this file, where you need to map the json file attributes and register the path of the json file of the data source, also you need to put the actual data source file into `/database/json`
    - visit `/api/users/ to filter the result
    - allowed filters in the query string are (all are case insensitive):
        - currency
        - status (available values are: 'authorized', 'declined' & 'refunded')
        - balanceMin
        - balanceMax
    and they can be combined together

    example: `http://you-server.test/api/users?currency=usd&status=declined&balanceMin=93`