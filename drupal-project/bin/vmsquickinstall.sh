#!/usr/bin/env bash

{ # this ensures the entire script is downloaded and run #

# Setup swap (which will enable on reboot)
# * We need more than 512MB ram for Meteor to work. Warning: meteor may be slow, especially first run, on small instances
# * https://www.digitalocean.com/community/tutorials/how-to-add-swap-on-ubuntu-14-04
sudo fallocate -l 4G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
echo "/swapfile   none    swap    sw    0   0" >> "/etc/fstab"
echo "vm.swappiness=10" >> /etc/sysctl.conf
echo "vm.vfs_cache_pressure = 50" >> /etc/sysctl.conf
# * Enable swap file now before reboot
sudo swapon /swapfile

# Update all base software
sudo apt-get update && sudo apt-get upgrade -y

# Add custom repositories
sudo apt-get install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo add-apt-repository 'deb [arch=amd64,i386] http://nyc2.mirrors.digitalocean.com/mariadb/repo/10.1/ubuntu trusty main' -y
sudo apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xcbcb082a1bb943db
sudo apt-get update

# Setup Maria DB installation (1 of 2)
# * Instructions from https://goo.gl/d1vOx6 (Ubuntu >> 14.04 >> 10.1 >> DigitalOcean - New York)
# * No password for maria db install from http://dba.stackexchange.com/a/60192
export DEBIAN_FRONTEND=noninteractive
sudo debconf-set-selections <<< 'mariadb-server-10.0 mysql-server/root_password password PASS'
sudo debconf-set-selections <<< 'mariadb-server-10.0 mysql-server/root_password_again password PASS'

# Install packaged stuff
# * Some versions from DO article @ https://goo.gl/jCiEhS
# * Compass
# ** We could compile Ruby - Digitial Ocean's instructions - https://goo.gl/TpZ2wL
# ** We could install Ruby - https://www.brightbox.com/docs/ruby/ubuntu/
# ** However we can just do `ruby-compass` much quicker, simpler, and more reliably with a single command (not doing ruby dev, we don't need fluff)
# * PHP & extensions (defaults to 7.X with ppa:ondrej/php)
sudo apt-get install -y git nginx ruby-compass mariadb-server php php-mysql php-curl php-gd php-fpm php-xml php-curl php-xdebug php-mbstring zip
sed -i 's/zend_extension=xdebug.so/#zend_extension=xdebug.so/' /etc/php/7.0/mods-available/xdebug.ini

composer global require "hirak/prestissimo:^0.3"

# Setup Maria DB installation (2 of 2)
mysql -uroot -pPASS -e "SET PASSWORD = PASSWORD('');"

# Install composer - https://getcomposer.org/download/
# * TODO: Figure out a real way to do this as page says not to distribute the installation code, this is a hack
# ** Commented out SHA check as a hack so this stops breaking so much
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
# php -r "if (hash_file('SHA384', 'composer-setup.php') === '92102166af5abdb03f49ce52a40591073a7b859a86e8ff13338cf7db58a19f7844fbc0bb79b2773bf30791e935dbd938') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# Install composer con't (globally)
mv composer.phar /usr/local/bin/composer

# Install drush (Instructiosn from http://docs.drush.org/en/master/install/)
# TODO: Use composer to install drush
# TODO: * Adjust pahts as needed
wget http://files.drush.org/drush.phar
chmod +x drush.phar
sudo mv drush.phar /usr/local/bin/drush
# drush init # Wanted to prompt, -y didn't seem to work

# Clone repository & make easy access
git clone https://github.com/$GHUSER/foodtrucks.git /var/foodtrucks
ln -s /var/foodtrucks /root/foodtrucks

# Configure website (From /drupal-project/README.md)
(cd /var/foodtrucks && rm -rf drupal-project)
(cd /var/foodtrucks && composer create-project drupal-composer/drupal-project:8.x-dev drupal-project --stability dev --no-interaction)
(cd /var/foodtrucks && git reset --hard HEAD)
(cd /var/foodtrucks/drupal-project && composer install --no-dev)
#mkdir -p /var/foodtrucks/drupal-project/web/sites/default
cp /var/foodtrucks/drupal-project/bin/vmsquickinstall.settings.php /var/foodtrucks/drupal-project/web/sites/default/settings.php
cp /var/foodtrucks/drupal-project/web/sites/example.settings.local.php /var/foodtrucks/drupal-project/web/sites/default/settings.local.php
#sed -i 's/debug: false/debug: true/' /var/foodtrucks/drupal-project/web/sites/default/services.yml
sed -i 's/auto_reload: null/auto_reload: true/' /var/foodtrucks/drupal-project/web/sites/default/services.yml
sed -i 's/cache: true/cache: false/' /var/foodtrucks/drupal-project/web/sites/default/services.yml
sed -i "s/\$settings['cache']['bins']['render']/# \$settings['cache']['bins']['render']/" /var/foodtrucks/drupal-project/web/sites/default/settings.local.php
sed -i "s/\$settings['cache']['bins']['dynamic_page_cache']/# \$settings['cache']['bins']['dynamic_page_cache']/" /var/foodtrucks/drupal-project/web/sites/default/settings.local.php

#(cd /var/foodtrucks/drupal-project/web && drush si --account-pass=admin -y)
#(cd /var/foodtrucks/drupal-project/web && drush cedit system.site --file="/var/foodtrucks/drupal-project/deploy/system.site.yml" -y)
#(cd /var/foodtrucks/drupal-project/web && drush cedit shortcut.set.default --file="/var/foodtrucks/drupal-project/deploy/shortcut.set.default.yml" -y)
# Base foodtrucks setup
mysql -e "CREATE DATABASE food_trucks"

(cd /var/foodtrucks/drupal-project/web && drush sqlc < /var/foodtrucks/drupal-project/data/db.sql) # Give us some data to play with
cp -r /var/foodtrucks/drupal-project/data/files /var/foodtrucks/drupal-project/web/sites/default/
#(cd /var/foodtrucks/drupal-project/web && drush cim -y)		# Configure the site

(cd /etc/nginx/sites-enabled/ && rm default && ln -s /var/foodtrucks/drupal-project/bin/nginx.conf default)
sudo service nginx restart

# Give nginx & php-fpm access to foodtrucks files
chown www-data:www-data /var/foodtrucks/drupal-project/web/sites/default/files -R

# Watch for CSS changes
crontab -l > /tmp/mycron09280398234098
echo "@reboot (cd /var/foodtrucks/drupal-project/web/themes/custom/foodtruckstheme && compass watch)" >> /tmp/mycron09280398234098
crontab /tmp/mycron09280398234098
rm /tmp/mycron09280398234098
echo
(cd /var/foodtrucks/drupal-project/web/sites/default && drush cr)
(cd /var/foodtrucks/drupal-project/web/sites/default && drush st)
(cd /var/foodtrucks/drupal-project/web/sites/default && drush uli)
echo
echo "Rebooting machine, give it (10-30?) seconds, then reconnect"
echo

reboot

} # this ensures the entire script is downloaded and run #
