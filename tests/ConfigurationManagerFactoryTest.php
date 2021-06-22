<?php

namespace Tests;

use Abdeslam\Configuration\ConfigurationManager;
use Abdeslam\Configuration\ConfigurationManagerFactory;
use PHPUnit\Framework\TestCase;

class ConfigurationManagerFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function factoryInit()
    {
        $config = ConfigurationManagerFactory::createPHPConfigurationManager(
            __DIR__ . '/config/config.php'
        );
        $this->assertInstanceOf(ConfigurationManager::class, $config);
        $this->assertTrue($config->has('app'));
        $this->assertTrue($config->has('credentials.username'));
    }
}
