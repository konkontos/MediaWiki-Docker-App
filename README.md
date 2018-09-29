# Overview

Docker app for **private** MediaWiki + MariaDB

The `images` folder is protected from users that haven't logged in.

A sample `LocalSettings.php` file is provided that has appropriate settings for protected uploads.

If you need a different behavior, provide your own `LocalSettings.php` and make sure that you edit the provided Apache configuration files (`apache_base_ssl...conf`):

- remove the `mod rewrite` commands from the `/var/www/html`  directory section
- delete the `/var/www/html/images` directory section

(**todo**: add non private configs and a variable to select them)


## Volumes and Environment vars.

Configuration is via `.env` file.

**See `sample.env` file, and inline comments, for all applicable variables.**

Copy this file into `.env` and edit accordingly (see inline comments).


### SSL certificates

If you have enabled SSL, then map a local volume with the necessary files and then provide values for the following vars:

SSL_CERT

SSL_KEY


## 1st time run 

**Before** the first run of the app, you'll need to be able to copy the container files under `/var/www/html` to the local folder you've setup in your `.env` file.

Edit `docker-compose.yml` :

- find your application ( `mediawiki` or `mediawikissl` )
- comment out the line that maps `WIKI_ROOT_FOLDER` to `/var/www/html`
- uncomment the line that maps `WIKI_ROOT_FOLDER` to `/hostmount`
- run the app
- enter the `mediawiki` container
- run: `cp -R * /hostmount`
- exit the container shell
- stop the app
- uncomment out the line that maps `WIKI_ROOT_FOLDER` to `/var/www/html`
- comment the line that maps `WIKI_ROOT_FOLDER` to `/hostmount`

You can now start / stop the app without further editing to `docker-compose.yml` .

Wiki files are available under your assigned root folder.


## Running / Stopping the app

A few convenience scripts are provided:

- start.sh: `start.sh [mediawiki|mediawikissl]`

    starts the designated app (from `docker-compose` file)
- start-rebuild.sh: `start-rebuild.sh [app]` 

    starts the designated app (from `docker-compose` file) and rebuilds the wiki image
- stop.sh

    stops the app
- enter_app_container.sh 

    enters into the wiki container shell
