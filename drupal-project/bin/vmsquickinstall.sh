#!/usr/bin/env bash

# TODO: Optomize this script?
# TODO: * There's a lot of `sudo apt-get update` which may be combinable
# TODO: ** May make less readable

{ # this ensures the entire script is downloaded and run #

# Update all software & install new
sudo apt-get update && sudo apt-get upgrade -y

# Install the simple stuff
# * Some versions from DO article @ https://goo.gl/jCiEhS
sudo apt-get install -y git apache2

# Install Maria DB
# * Instructions from https://goo.gl/d1vOx6 (Ubuntu >> 14.04 >> 10.1 >> DigitalOcean - New York)
# * No password for maria db install from http://dba.stackexchange.com/a/60192
sudo apt-get install software-properties-common
sudo apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xcbcb082a1bb943db
sudo add-apt-repository 'deb [arch=amd64,i386] http://nyc2.mirrors.digitalocean.com/mariadb/repo/10.1/ubuntu trusty main'
sudo apt-get update
export DEBIAN_FRONTEND=noninteractive
sudo debconf-set-selections <<< 'mariadb-server-10.0 mysql-server/root_password password PASS'
sudo debconf-set-selections <<< 'mariadb-server-10.0 mysql-server/root_password_again password PASS'
sudo apt-get install -y mariadb-server
mysql -uroot -pPASS -e "SET PASSWORD = PASSWORD('');"

# Install PHP 5.6
# * https://www.dev-metal.com/install-setup-php-5-6-ubuntu-14-04-lts/
sudo add-apt-repository ppa:ondrej/php5-5.6
sudo apt-get update
sudo apt-get install -y python-software-properties
sudo apt-get update
sudo apt-get install -y php5

# Install PHPMySQL support
sudo apt-get install -y php5-mysql

# Install composer
php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php
php -r "if (hash('SHA384', file_get_contents('composer-setup.php')) === '7228c001f88bee97506740ef0888240bd8a760b046ee16db8f4095c0d8d525f2367663f22a46b48d072c816e7fe19959') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"

# Clone repository
git clone https://github.com/codeforeauclaire/foodtrucks.git /root/foodtrucks

# Configure website
mkdir -p /root/foodtrucks/drupal-project/web/sites/default/
cp /root/foodtrucks/drupal-project/bin/vmsquickinstall.settings.php /root/foodtrucks/drupal-project/web/sites/default/settings.php

} # this ensures the entire script is downloaded and run #
