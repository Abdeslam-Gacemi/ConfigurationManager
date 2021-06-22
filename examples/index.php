<?php

use Abdeslam\Configuration\Configuration;
use Abdeslam\Configuration\Loaders\PHPConfigurationLoader;

require __DIR__.'/../vendor/autoload.php';

$loader = new PHPConfigurationLoader(__DIR__);
$config = new Configuration($loader, 'conf.php');

/* var_dump($config->get('app'));

var_dump($config->get('database.development')); */

var_dump($config->get('non_existant_key', 'hello'));
