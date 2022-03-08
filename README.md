## Bundles and components ##
* Monolog.
* Doctrine.
* symfony/maker.
* Symfony/cli.
* Annotations.
* Validation
* PHPUnit.
* FosRestBundle.
* Twig.

## Docker ##
Up docker container:
```
docker-compose up -d
``` 

Enter inside docker container:
```
docker-compose exec php sh
```

Once inside the container, install composer dependencies:
```
composer install
```

Create your local env file. .env.local and set database connection
```
DATABASE_URL="mysql://laLiga:laLiga@laLiga-mysql:3306/laLiga?serverVersion=5.7&charset=utf8mb4"
MAILER_DSN=smtp://mailhog:1025
```

After that run the following command to create our database:
```
bin/console doctrine:database:create
```

Once database is created, run migrations:
```
bin/console doctrine:migrations:migrate
``` 

**Now, the api is ready to work!!**
However you can load the dump placed at:
*ProjectRoot/config/postman*

## POSTMAN COLLECTION ##
You will find a Postman collection at folder 
*ProjectRoot/config/postman* 
with the whole actions you requests at user's guide.

**Clubes**
- Dar de alta un club.
- Dar de alta un jugador en el club.
- Dar de alta un entrenador en el club.
- Modificar el presupuesto de un club.
- Dar de baja un jugador del club.
- Dar de baja un entrenador del club.
- Listar jugadores de un club con posibilidad de filtrar por una de las propiedades (por ejemplo nombre) y con paginación

**Jugadores**
- Dar de alta un jugador sin pertenecer a un club.

**Entrenadores**
- Dar de alta un entrenador sin pertenecer a un club.


## TEST ##
Create database for testing:
``` 
php bin/console doctrine:schema:update --env=test --force
```

Update test database tables:
``` 
php bin/console doctrine:migrations:migrate --env=test
```

Run tests:
```
php bin/phpunit tests
```
