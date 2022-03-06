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
Once inside container, run the following command to create our database:
```
bin/console doctrine:database:create
```

## POSTMAN COLLECTION ##
You will find a Postman collection at folder ProjectRoot/config/postman with the whole actions you requests at user's guide.

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
