# ConfigurationManager package
**[ConfigurationManager](https://github.com/Abdeslam-Gacemi/ConfigurationManager.git)** is a simple, powerful and flexible **PHP**, **JSON** and **XML** configuration file loader.

## Table of contents
  - [Requirements](#requirements)
  - [Installation](#installation)
    - [1. Using Composer](#1-using-composer)
    - [2. Manually](#2-manually)
  - [Usage](#usage)
    - [1. using the class ConfigurationManager](#1-using-the-class-configurationmanager)
    - [2. using the static factory](#2-using-the-static-factory)
  - [Accessing values](#accessing-values)
  - [Customization](#customization)

## Requirements

* php: ^8.0

## Installation

### 1. Using Composer

You can install the library via [Composer](https://getcomposer.org/).

```
php composer.phar require abdeslam/configuration-manager
```

or

```
composer require abdeslam/configuration-manager
```

### 2. Manually

If you're not using Composer, you can also clone `Abdeslam/ConfigurationManager` repository into your directory:

```
git clone https://github.com/Abdeslam-Gacemi/ConfigurationManager.git
```

However, using Composer is recommended as you can easily keep the library up-to-date.

## Usage

### 1. using the class `ConfigurationManager`

let's assume that is your PHP configuration file :
```php
<?php

// config.php

return [
    'database' => [
        'driver' => 'mysql',
        'username' => 'user',
        'password' => '1234',
    ],
    'debug' => true
];
```

```php
<?php

use Abdeslam\ConfigurationManager\ConfigurationManager;
use Abdeslam\ConfigurationManager\Loaders\PHPConfigurationLoader;

require '/path/to/autoload.php';

$loader = new PHPConfigurationLoader();
$manager = new ConfigurationManager();
$manager->addLoader($loader, '/path/to/config.php')->load();

echo $manager->get('database.driver'); // output: 'mysql'
```

or loading multiple configuration files :
to load multiple configuration files, add as many files as you want as arguments to the method `ConfigurationManager::addLoader()` after the the first argument (the loader).

```php
<?php

use Abdeslam\ConfigurationManager\ConfigurationManager;
use Abdeslam\ConfigurationManager\Loaders\PHPConfigurationLoader;

require '/path/to/autoload.php';

$loader = new PHPConfigurationLoader();
$manager = new ConfigurationManager();
// loading multiple configuration files
$manager->addLoader($loader, '/path/to/config.php', '/path/to/config2.php')->load();

// rest of the code
```

the available methods through the `ConfigurationManager`:

```php
<?php

use Abdeslam\ConfigurationManager\ConfigurationManager;
use Abdeslam\ConfigurationManager\Loaders\PHPConfigurationLoader;

require '/path/to/autoload.php';

$loader = new PHPConfigurationLoader();
$manager = new ConfigurationManager();
$manager->addLoader($loader, '/path/to/config.php')->load();

echo $manager->get('database.driver'); // output: 'mysql'
$manager->get('debug'); // returns: (bool) true
$manager->get('non_existing_key'); // throws an exception
$manager->has('debug')); // returns: (bool) true
$manager->has('non_existing_key'); // returns: (bool) false
$manager->remove('debug'); // removes 'debug' item from the configuration items array

$manager->all(); // returns: the array of all the configuration items
$manager->getLoaders(); // returns: the array of all the registered loaders
$manager->getLoader(PHPConfigurationLoader::class); // returns: the loader instance
$manager->hasLoader(PHPConfigurationLoader::class); // returns: true
$manager->getLoadedFiles(); // returns: an array of all the loaded files
$manager->set('api_key' => 'some_key'); // adds an item with the key 'api_key' and the value 'some_key' to the configuration items
$manager->merge(['verbose' => true]); // merges the configuration items array with the array given as an argument
$manager->merge($anotherManagerInstance); // merges the array of items of the original manager with the array of items of the managers supplies as an argument
$manager->reset(); // resets the object to the initial state
```

### 2. using the static factory

to set a specific loader to use, pass one of the the following strings as a first argument to the `ConfigurationManagerFactory::create()` static factory method:
* 'php': to use `PHPConfigurationLoader::class` as a default loader
* 'json': to use `JSONConfigurationLoader::class` as a default loader
* 'xml': to use `XMLConfigurationLoader::class` as a default loader

```php
<?php

use Abdeslam\ConfigurationManager\ConfigurationManagerFactory;

require '/path/to/autoload.php';

$manager = ConfigurationManagerFactory::create(
  'php',
  '/path/to/config.php',
  '/path/to/config2.php'
);
$manager->load();

// rest of the code
```

## Accessing values

`ConfigurationManager::get()` and `ConfigurationManager::has()` have **compound key resolving** capability separated by '.' by default and the key separator can be changed, which means :

```php
<?php

// config.php
return [
  'address' => [
    'country' => 'Algeria',
    'city' => [
      'name' => 'Algiers',
      'postal_code' => '16000'
    ]
  ]
];

// client code
$manager->get('address.city.name'); // returns: 'Algiers'
$manager->setKeySeparator('_'); // changes the key separator to '_'
$manager->get('address_country'); // returns: 'Algeria'
$manager->getKeySeparator(); // returns: '_'
```

## Customization

you can create your own configuration loader by creating a class that implements `ConfigurationLoaderInterface::class` :

```php
<?php

// CustomConfigurationLoader.php

use Abdeslam\ConfigurationManager\Contracts\ConfigurationLoaderInterface;

class CustomConfigurationLoader implements ConfigurationLoaderInterface
{
  /**
   * @inheritDoc
   */
  public function (string ...$filepaths): array
  {
    return ['hello' => 'world'];
  }
}

// client code
$manager->addLoader(new CustomLoader(), '/path/to/configuration/file');
$manager->load();
$manager->get('hello'); // returns: 'world'

// using the factory
ConfigurationManagerFactory::addConfigurationLoader('custom_loader', CustomConfigurationLoader::class);
$manager = ConfigurationManagerFactory::create('custom_loader', '/path/to/configuration/file');
$manager->load();
$manager->get('hello'); // returns: 'world'

// overriding Factory loaders
ConfigurationManagerFactory::addConfigurationLoader('php', CustomConfigurationLoader::class);
$manager = ConfigurationManagerFactory::create('php', '/path/to/config.php');
$manager->load();
$manager->get('hello'); // returns: 'world'

```

> Made with love :heart: