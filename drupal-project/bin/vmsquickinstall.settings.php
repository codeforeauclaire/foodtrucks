<?php

$databases['default']['default'] = array (
	'database' => 'food_trucks',
	'username' => 'root',
	'password' => '',
	'prefix' => '',
	'host' => 'localhost',
	'port' => '3306',
	'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
	'driver' => 'mysql',
);
$config_directories['sync'] = '../deploy';
$settings['hash_salt'] = hash('sha256', serialize($databases));

