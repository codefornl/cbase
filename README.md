# cbase
Clarity Case Studies (https://clarity.codefor.nl) is a project to create and share curated sets (cbases) of use cases of civic tech and gov tech. It runs on top of the cbase API at https://cbase.codefor.nl, which is an open HAL complient API.

## environment variables

## Run a development environment

The development environment will connect to the cbase.codefor.nl BASE_URI by default. Override it if you have a local setup for the API too.

```
docker build -t cbase_dev_local:0.0.1 .
docker run -v $(pwd)/public:/var/www/html/public -v $(pwd)/private:/var/www/html/private -p 8080:80 cbase_dev_local:0.0.1
```

You can then access the site at http://localhost:8080 and make changes in your local php files.

## Using Docker Compose in development

To create and run cbase `docker-compose up -d`

### Creating the database in development

To go into the docker `docker exec -it <container> /bin/bash`

Go to the private dir of the mariadb container and run `mysql -u user -p test < create_tables.dist.sql`

Go to the private/scripts dir of the cbase container and run `php import_from_api.php`
