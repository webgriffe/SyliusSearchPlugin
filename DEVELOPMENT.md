# Testing

+ [Git](https://git-scm.com/downloads)
+ [Docker](https://docs.docker.com/get-docker/)
+ [Docker Compose](https://github.com/docker/compose)
+ [Composer v2](https://getcomposer.org/download/)
+ [Symfony Cli](https://symfony.com/download)
+ [Yarn](https://yarnpkg.com/cli/install)
+ Node 14, you can use [NVM](https://github.com/nvm-sh/nvm)
+ PHP 8.0

## Installation

From the plugin root directory, run the following commands:

```bash
composer install
docker-compose up -d
(cd tests/Application && yarn install)
(cd tests/Application && yarn build)
(cd tests/Application && bin/console assets:install public -e test)
(cd tests/Application && bin/console doctrine:database:drop --force -e test --if-exists)
(cd tests/Application && bin/console doctrine:database:create -e test)
(cd tests/Application && bin/console doctrine:schema:create -e test)
```

## Usage

### Running plugin tests

  - PHPUnit

    ```bash
    vendor/bin/phpunit
    ```

  - PHPSpec

    ```bash
    vendor/bin/phpspec run
    ```
    
  - PHPStan
  
    ```bash
    vendor/bin/phpstan analyse src
    ```

### Opening Sylius with the plugin

- Using `test` environment:

    ```bash
    (cd tests/Application && bin/console sylius:fixtures:load -e test)
    APP_ENV=test symfony server:start --port=8080 --dir=tests/Application/public --daemon
    ```
    
- Using `dev` environment:

    ```bash
    (cd tests/Application && bin/console sylius:fixtures:load -e dev)
    APP_ENV=dev symfony server:start --dir=tests/Application/public --daemon
    ```

### Reindex Elasticsearch

- Using `test` environment:

    ```bash
    (cd tests/Application && bin/console monsieurbiz:search:populate -e test)
    ```
    
- Using `dev` environment:

    ```bash
    (cd tests/Application && bin/console monsieurbiz:search:populate -e dev)
    ```
