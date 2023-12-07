# Transaction Service

## Setup project
1. Build/run containers
    ```
    docker-compose up --build
    ```

2. Enter to container
    ```
    2.1 Show docker containers
    -   docker container ls
    2.2 Enter to container
    -   docker exec -it <CONTAINER ID> sh
    ```

3. Run commands for project installation:
    ```
    composer install
    php bin/console doctrine:migrations:migrate
    ```

4. Setup tests
    ```
    php bin/console --env=test doctrine:database:create
    php bin/console --env=test doctrine:schema:create
    ```

5. Run tests
    ```
    php bin/phpunit
    ```

6. Enjoy :)
