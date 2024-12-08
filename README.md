## To run docker compse type

```
docker compose up

```

## After docker container is up and runngin than you need to:

First, you will need to install WordPress. After installation, you need to log in to your admin account. After login you will need to install this plugin:

```
JWT Authentication for WP-API
```

JWT Authentication for WP-API is used for getting bearer tokens for consuming.

WT Authentication for WP-API need JWT_AUTH_SECRET_KEY to work.

```
You will need to open docker shell and add this line to your
/www/html/wp-config.php file
define( 'JWT_AUTH_SECRET_KEY',       getenv_docker('JWT_AUTH_SECRET_KEY', 'your-top-secret-key') );
define( 'JWT_AUTH_CORS_ENABLE',       getenv_docker('JWT_AUTH_CORS_ENABLE', true) );

```

JWT_AUTH_CORS_ENABLE is here because of CORS. It should work without it, but for testing purpose you can set it to **TRUE**.

Than you need to this step: Go to /wp-content/plugins/gambling-rest-api and install composer dependencies.

## To install composer dependencies run comand below

```
composer install

```

When you install composer dependencies then activate the plugin. Plugin activation will create several database tables. When the plugin is activated go to plugin and click seed. The seed will input demo data into the database so API can be consumed from the Next.js side.

After you have account for WP (account do not need to have admin permisions), and have JWT and Gambling rest api pluins you can go to **/gambling-app**

Inside of **/gambling-app** you should first install npm dependencies.

```
npm run install
or
pnpm run install

after instal you can go:
npm run dev

```
