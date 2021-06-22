<?php

use Abdeslam\Configuration\ConfigurationManager;
use Abdeslam\Configuration\ConfigurationManagerFactory;
use Abdeslam\Configuration\Loaders\PHPConfigurationLoader;

require __DIR__.'/../vendor/autoload.php';

$loader = new PHPConfigurationLoader();
$config = new ConfigurationManager();
$config->addLoader($loader, __DIR__ . '/config/conf.php');

echo $config->get('app');

$config = ConfigurationManagerFactory::createJSONConfigurationManager(__DIR__ . '/config/config.json');
$config->setKeySeparator('_');
echo $config->get('debug_verbose');
echo $config->get('env');
