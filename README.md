# BileMo
BileMo is a company offering a whole selection of high-end mobile phones.

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/2d80c00dc2294ef2ad47c5c2fc9ede48)](https://www.codacy.com/gh/guicima/BileMo/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=guicima/BileMo&amp;utm_campaign=Badge_Grade)

## Set up the environnement
### Install dependencies

Install Docker
[Get Docker](https://docs.docker.com/get-docker/)

Install Composer
[Get Composer](https://getcomposer.org/)

### Start the project

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

### Access the project

|Container | Address |
|--|:--:|
| BileMo | [http://localhost:8000/](http://localhost:8000/) |
| BileMo documentation | [http://localhost:8000/api/doc](http://localhost:8000/api/doc) |