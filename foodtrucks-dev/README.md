#### Initial Setup Steps
* Clone this project and run `composer install` from its root folder to install drupal core and dependencies

```
git clone git@github.com:codeforeauclaire/foodtrucks.git
cd foodtrucks/foodtrucks-dev
composer install
```
* Configure a database and db user in your server. The database name, user credentials, the path
to the deploy folder, and a `$settings` hash need to be entered into the site `settings.php` file  
(here is an example `settings.php` snippet to append to the `web/sites/default/settings.php` file)

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
* from the web directory use drush to install the site  
```
from web directory
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
from web directory
cat ../deploy/system.site.yml
drush cedit system.site

cat ../deploy/shortcut.set.default.yml
drush cedit shortcut.set.default
```
#### Import Configuration
* finally, import config using drush

```
from web directory
drush cim  
```

#### some final notes
* from web folder, `drush cex` will export config to the deploy folder
* from project root folder, `composer require drupal/MODULE_NAME:8.x` will usually install the latest version of that module or theme in their contrib folder and then update the composer.json and composer.lock files
* from project root folder, commit and push changes in deploy config, composer.json and composer.lock

```
cd to your project root, above the drupal root.
from web directory
cd ../..
git add .
git commit -m "YOUR COMMIT MESSAGE"
git push
```

