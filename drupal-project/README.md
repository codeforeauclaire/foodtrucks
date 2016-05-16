# Food Trucks :: Drupal Project

Provides food truck administration and an API for the consumer user interface.

## VMS Easy setup development environment

Use these instructions to setup a temporary* development environment of this project.

1. Fork this repository
1. Create a new [Virtual Machine](http://vms.codeforeauclaire.org/) (1gb+ recommended) >> SSH in >> Run vms quick install:
 1. `export GHUSER='AnthonyAstige'` (Replace AnthonyAstige with your username)
 1. `curl -L -o- https://rawgit.com/$GHUSER/foodtrucks/master/drupal-project/bin/vmsquickinstall.sh | bash`
1. See app running
 1. Visit http://{vms-ip} in your browser
1. Options to move forward
 1. Play with UI: Use `(cd /foodtrucks/drupal-project/web && drush uli 1)` for login link to login as admin
 1. Be a dev: Edit a file >> refresh page >> see changes in your browser

*For a permanent development environment we recommend you read the referenced script above to install locally.

## TODO

* [General Specs](../SPECS.md)

## Prerequisites

*  php 5.6 (minimum, with extensions: php5-mysql, php5-gd, php5-curl, php5-fpm)
*  A MySQL server (MariaDB recommended)
*  Webserver (nginx recommended, apache will probably work too)

## Usage

First you need to [install composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

> Note: The instructions below refer to the [global composer installation](https://getcomposer.org/doc/00-intro.md#globally).
You might need to replace `composer` with `php composer.phar` (or similar) 
for your setup.

#### Initial Setup Steps
* Clone this repository and delete its `drupal-project` directory
* Create a new project directory with the `composer create-project` command
* Restore this repository's files with `git reset`
* Install this reposirtory's custom project dependencies with `composer install`, this command will take a while to finish

```
git clone git@github.com:codeforeauclaire/foodtrucks.git
cd foodtrucks
rm -rf drupal-project
composer create-project drupal-composer/drupal-project:8.x-dev drupal-project --stability dev --no-interaction
git reset --hard HEAD
cd drupal-project
composer install
```

* Configure your server, set the site root to the `drupal-project/web/` directory
* Configure a database and db user in your server. The database name, user credentials, the path
to the deploy folder, and a `$settings` hash need to be entered into the site `settings.php` file  
(here is an example `settings.php` snippet to append to the `drupal-project/web/sites/default/settings.php` file)

```
$databases['default']['default'] = array (
  'database' => 'DB_NAME',
  'username' => 'DB_USERNAME',
  'password' => 'DB_PASSWORD',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);
$config_directories['sync'] = '../deploy';
$settings['hash_salt'] = hash('sha256', serialize($databases));
```
then replace the `DB_NAME`, `DB_USERNAME`, and `DB_PASSWORD` placeholders  

#### Populate the database with a fresh install (either `install.php` or `drush si`)
TODO: Update here to use stuff from quickinstall script
```
(cd /root/foodtrucks/drupal-project/web && drush cedit system.site --file="/root/foodtrucks/drupal-project/deploy/system.site.yml" -y)
(cd /root/foodtrucks/drupal-project/web && drush cedit shortcut.set.default --file="/root/foodtrucks/drupal-project/deploy/shortcut.set.default.yml" -y)
```


* from the web directory use drush to install the site  
```
cd drupal-project/web
drush si --account-pass=admin -y
```
(if you don't have drush 8.x installed I believe you can use the one provided in the vendor/drush directory:   `../vendor/drush/drush/drush`)

* with a browser go to  
```
http://yoursite.name/core/install.php
```

#### Change Database UUID's to match our configuration UUID's
* lookup `system.site` values from deploy folder and then edit the site db-config to replace the uuid with the one from the `deploy/system.site.yml` file
* delete the last 2 lines with the hash from the db-config `_core:  default_config_hash:`
* repeat for the shortcut if needed

```
cd drupal-project/web
cat ../deploy/system.site.yml
drush cedit system.site

cat ../deploy/shortcut.set.default.yml
drush cedit shortcut.set.default
```
#### Import Configuration
* finally, import config using drush

```
cd drupal-project/web
drush cim  
```

#### some final notes
* from web folder, `drush cex` will export config to the deploy folder
* from project root folder, `composer require drupal/MODULE_NAME:8.x` will usually install the latest version of that module or theme in their contrib folder and then update the composer.json and composer.lock files
* from project root folder, commit and push changes in deploy config, composer.json and composer.lock

```
cd to your project root, above the drupal root.
cd ../.. from web directory
git add .
git commit -m "YOUR COMMIT MESSAGE"
git push
```

