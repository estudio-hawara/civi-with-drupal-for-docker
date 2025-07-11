# Civi with Drupal for Docker

## Install

To start using this container, start by cloning the repository:

```bash
git clone https://github.com/estudio-hawara/civi-with-drupal-for-docker.git
cd civi-with-drupal-for-docker
```

## Configure

Once you have cloned the repository, create a new `.env` file and set your safe passwords there.

```bash
cp env.example .env
```

Make sure this variables are properly set:

```bash
# MySQL
DB_HOST=mysql
DB_PORT=3306
DB_NAME=drupal_database
DB_USER=drupal_user
DB_PASSWORD=database_password
```

## Docker Container

The first time that you start the container, do that with `--build` so that the image is created. This will load the container with the minimum settings. It may not be what you need, so keep reading.

```bash
docker compose up -d --build
```

To start a container with an already built image you can just run:

```bash
docker compose up -d
```

Which is equivalent to loading the default `docker-compose.yml`:

```bash
docker compose \
    -f docker-compose.yml \
    up -d
```

### Binding the Port to the Host

To start the container with a host port binded to the host, you should add the `docker-compose.port.yml` file to your command:

```bash
docker compose \
    -f docker-compose.yml \
    -f docker-compose.port.yml \
    up -d
```

The host port to be used can be configured by changing the value of the **WEB_HOST_PORT** environment variable. It's default value is 8080.

Now you should be able to load the application navigating to: [http://localhost:8080](http://localhost:8080) from the host.

### Enabling Traefik Support

To start the container with Traefik support, use the `docker-compose.traefik.yml` file:

```bash
docker compose \
    -f docker-compose.yml \
    -f docker-compose.traefik.yml \
    up -d
```

Traefik support works very well with its corresponding sibling project: [traefik-for-docker](https://github.com/estudio-hawara/traefik-for-docker).

### Enabling Container Volumes

To start the container with volumes, so that the files are preserved after restarts, add `docker-compose.container-volumes.yml`.

```bash
docker compose \
    -f docker-compose.yml \
    -f docker-compose.container-volumes.yml \
    up -d
```

### Enabling Local Volumes

To start the container with local volumes, so that the container extensions and modules are accessible from the host, add `docker-compose.local-volumes.yml`.

```bash
docker compose \
    -f docker-compose.yml \
    -f docker-compose.local-volumes.yml \
    up -d
```

Then you'll have access to this folders that contain CiviCRM extensions and Drupal modules and themes in the **volumes** folder.

### Enabling XDebug

To enable XDebug, set the `INSTALL_XDEBUG` environment variable to `1` when building the image. It's important to do this at build time because otherwise your container won't even have XDebug installed.

```bash
INSTALL_XDEBUG=1 docker compose up -d --build
```

### The `dc` Helper

To standardize calling Docker from the project root, the `dc` filename has been added to this project's `.gitignore`.

You are encouraged to create it either if you will be only using the standard file:

```bash
#!/bin/bash
docker compose \
    -f docker-compose.yml \
    "$@"
```

... or if you will also be using the open port file:

```bash
#!/bin/bash
docker compose \
    -f docker-compose.yml \
    -f docker-compose.port.yml \
    "$@"
```

... or for usage with Traefik:

```bash
#!/bin/bash
docker compose \
    -f docker-compose.yml \
    -f docker-compose.traefik.yml \
    "$@"
```

Give executing permissions to the new file:

```bash
chmod +x dc
```

Now, you will be able to use this short hand:

```bash
./dc up -d --build
```

## Install Drupal and CiviCRM

Your Drupal service should now be ready for use, but you'll still need to follow both Drupal and CiviCRM installers step by step. To run both installations in an automated way, you can run:

```bash
./dc exec -u root drupal \
    install-drupal-and-civicrm
```

> Note the `-u root` that is necesary as this command installs software and needs privileges to do so.

### Install CiviCRM Extensions

If you are working with local volumes and you added a new extension to your `volume/civicrm-extensions` folder, you can enable with:

```bash
./dc exec drupal \
    cv ext:enable \
        --url=http://aicivi.localhost \
        your_extension_name
```

### Install Drupal Modules and Themes

If you are working with local volumes and you added a new module to your `volume/drupal-modules` folder, you can enable with:

```bash
./dc exec drupal \
    drush en \
        your_module_name -y
```

The same holds for themes.


## Unit Tests

If you are going to use the container for development, you may want to enable unit tests. To enable support for PHPUnit, first set up the credentials of your test database:

```bash
# MySQL / PHPUnit
TEST_DB_HOST=mysql
TEST_DB_PORT=3306
TEST_DB_NAME=test_database
TEST_DB_USER=test_user
TEST_DB_PASSWORD=test_password
```

Then install **phpunit**:

```bash
./dc exec -u root drupal \
    install-phpunit
```

> Note the `-u root` that is necesary as this command installs software and needs privileges to do so.

... and initialize the test database:

```bash
./dc exec drupal \
    initialize-test-db
```

Now you can run the tests from each of the extensions that you have installed in your system by running:

```bash
# Let's say you want to test "civirules"

./dc exec -w /opt/drupal/web/sites/default/files/civicrm/ext/civirules drupal \
    phpunit
```
