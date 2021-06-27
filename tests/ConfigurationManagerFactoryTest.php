<?php

/**
* @author Abdeslam Gacemi <abdobling@gmail.com>
*/

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\CustomLoader;
use Abdeslam\ConfigurationManager\ConfigurationManager;
use Abdeslam\ConfigurationManager\ConfigurationManagerFactory;
use Abdeslam\ConfigurationManager\Loaders\PHPConfigurationLoader;
use Abdeslam\ConfigurationManager\Loaders\XMLConfigurationLoader;
use Abdeslam\ConfigurationManager\Loaders\JSONConfigurationLoader;

class ConfigurationManagerFactoryTest extends TestCase
{

    /**
     * @test
     */
    public function factoryCreateManagerWithPHPLoader()
    {
        $manager = ConfigurationManagerFactory::create(
            'php',
            __DIR__ . '/config/config.php'
        );
        $this->assertInstanceOf(ConfigurationManager::class, $manager);
        $this->assertTrue($manager->hasLoader(PHPConfigurationLoader::class));
        $this->assertInstanceOf(
            PHPConfigurationLoader::class,
            $manager->getLoader(PHPConfigurationLoader::class)
        );
        $manager->load();
        $this->assertTrue($manager->has('app'));
        $this->assertTrue($manager->has('credentials.username'));
    }

    /**
     * @test
     */
    public function factoryCreateManagerWithJSONLoader()
    {
        $manager = ConfigurationManagerFactory::create(
            'json',
            __DIR__ . '/config/config.json'
        );
        $this->assertTrue($manager->hasLoader(JSONConfigurationLoader::class));
        $this->assertInstanceOf(
            JSONConfigurationLoader::class,
            $manager->getLoader(JSONConfigurationLoader::class)
        );
        $manager->load();
        $this->assertTrue($manager->has('environment'));
        $this->assertTrue($manager->has('author.name'));
    }
    
    /**
     * @test
     */
    public function factoryCreateManagerWithXMLLoader()
    {
        $manager = ConfigurationManagerFactory::create(
            'xml',
            __DIR__ . '/config/config.xml'
        );
        $this->assertTrue($manager->hasLoader(XMLConfigurationLoader::class));
        $this->assertInstanceOf(
            XMLConfigurationLoader::class,
            $manager->getLoader(XMLConfigurationLoader::class)
        );
        $manager->load();
        $this->assertTrue($manager->has('database'));
        $this->assertTrue($manager->has('environments.development'));
    }

    /**
     * @test
     */
    public function ConfigurationManagerFactoryAddConfigurationLoader()
    {
        // overriding 'php' ConfigurationLoader alias
        configurationManagerFactory::addConfigurationLoader(
            'php', 
            CustomLoader::class
        );

        $manager = ConfigurationManagerFactory::create(
            'php',
            __DIR__ . '/config/config.php'
        )->load();

        $this->assertSame(['hello' => 'world'], $manager->all());
    }
}
