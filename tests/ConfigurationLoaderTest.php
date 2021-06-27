<?php

namespace Tests;

use Abdeslam\Configuration\Exceptions\ConfigurationFileNotFoundException;
use Abdeslam\Configuration\Exceptions\InvalidConfigurationContentException;
use Abdeslam\Configuration\Exceptions\InvalidConfigurationFileException;
use Abdeslam\Configuration\Loaders\JSONConfigurationLoader;
use Abdeslam\Configuration\Loaders\PHPConfigurationLoader;
use Abdeslam\Configuration\Loaders\XMLConfigurationLoader;
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
        $configArray = $loader->load([__DIR__ . '/config/config.php']);
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
    public function loaderValidateFile()
    {
        $loader = new JSONConfigurationLoader();
        $reflect = new ReflectionClass($loader);
        $method = $reflect->getMethod('validateFile');
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

    /**
     * @test
     */
    public function loaderLoadFileWithInvalidContent()
    {
        $loader = new PHPConfigurationLoader();
        $this->expectException(InvalidConfigurationFileException::class);
        $loader->load([__DIR__ . '/config/config.json']);

        $loader = new JSONConfigurationLoader();
        $this->expectException(InvalidConfigurationFileException::class);
        $loader->load([__DIR__ . '/config/config.php']);
    }

    /**
     * @test
     */
    public function loaderLoadXmlConfiguration()
    {
        $loader = new XMLConfigurationLoader();
        $configArray = $loader->load([__DIR__ . '/config/config.xml']);
        $expected = [
            'database' => [
                'username' => 'admin',
                'password' => 1234
            ],
            'environments' => [
                'development' => [
                    'debug' => true,
                    'verbose' => true,
                    'verbosity' => 1.2
                ]
            ]
        ];
        $this->assertSame($expected, $configArray);

    }
}
