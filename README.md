# BileMo
BileMo is a company offering a whole selection of high-end mobile phones.

## Set up the environnement
**Install dependencies**

Install Docker
[Get Docker](https://docs.docker.com/get-docker/)

Install Composer
[Get Composer](https://getcomposer.org/)

**Start the project**


Install composer dependencies
```sh
composer install
```

Generate JWT keypair
```sh
php bin/console lexik:jwt:generate-keypair
```

Start containers
```sh
docker-compose up
```
or
```sh
docker-compose up -d
```
to run in detached mode.

Load database
```sh
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load -n
```

Start project in local
```sh
symfony serve
```

**Access the project**

|Container | Address |
|--|:--:|
| BileMo | [http://localhost:8000/](http://localhost:8000/) |