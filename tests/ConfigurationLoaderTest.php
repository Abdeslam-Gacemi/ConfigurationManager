<?php

namespace Tests;

use Abdeslam\Configuration\Exceptions\ConfigurationFileNotFoundException;
use Abdeslam\Configuration\Loaders\JSONConfigurationLoader;
use Abdeslam\Configuration\Loaders\PHPConfigurationLoader;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ConfigurationLoaderTest extends TestCase
{
    /**
     * @test
     */
    public function loaderInit()
    {
        $loader = new PHPConfigurationLoader();
        $configArray = $loader->load(__DIR__ . '/config/config.php');
        $this->assertIsArray($configArray);
        $this->assertSame('my-app', $configArray['app']);
        $this->assertSame('user1', $configArray['credentials']['username']);
    }

    /**
     * @test
     */
    public function loaderLoadFile()
    {
        $loader = new JSONConfigurationLoader();
        $reflect = new ReflectionClass($loader);
        $method = $reflect->getMethod('loadFile');
        $method->setAccessible(true);
        $configArray = $method->invokeArgs(
            $loader,
            [__DIR__ . '/config/config.json']
        );
        $this->assertIsArray($configArray);
        $this->assertArrayHasKey('debug', $configArray);
        $this->assertSame(true, $configArray['debug']);
        $method->setAccessible(false);
    }

    /**
     * @test
     */
    public function loaderFileExists()
    {
        $loader = new JSONConfigurationLoader();
        $reflect = new ReflectionClass($loader);
        $method = $reflect->getMethod('checkFileExists');
        $method->setAccessible(true);
        $fileExists = $method->invokeArgs(
            $loader,
            [__DIR__ . '/config/config.json']
        );
        $this->assertNull($fileExists);

        $this->expectException(ConfigurationFileNotFoundException::class);
        $method->invokeArgs(
            $loader,
            ['non_existent_configuration_file']
        );
        $method->setAccessible(false);
    }
}
