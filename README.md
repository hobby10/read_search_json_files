- To run project follow these steps please:
    + composer install
    + cp .env.example .env
    + php artisan key:generate
    + ./vendor/bin/sail build
    + ./vendor/bin/sail up
    + ./vendor/bin/sail artisan command:read_provider_files_data        //to read and cache json files


- To review task configurations review (config/data_providers.php file)

- To run tests 
    ./vendor/bin/sail artisan test

- To test users api hit url (/api/v1/users) with different filters


- If we need to add new filters we can modify in (app/Services/SearchService.php file) and update (config/data_providers.php file)
- To working on new json file, add it in (storage/data_files/) and update (config/data_providers.php file)




- There is another appropriate to handle this case by using 
mongodb or mysql (json data type) 
and 
elasticsearch