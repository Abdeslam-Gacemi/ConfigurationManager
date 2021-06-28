<?php

/**
* @author Abdeslam Gacemi <abdobling@gmail.com>
*/

namespace Tests;

use ReflectionClass;
use PHPUnit\Framework\TestCase;
use Abdeslam\ConfigurationManager\Loaders\ENVConfigurationLoader;
use Abdeslam\ConfigurationManager\Loaders\PHPConfigurationLoader;
use Abdeslam\ConfigurationManager\Loaders\XMLConfigurationLoader;
use Abdeslam\ConfigurationManager\Loaders\JSONConfigurationLoader;
use Abdeslam\ConfigurationManager\Exceptions\InvalidConfigurationFileException;
use Abdeslam\ConfigurationManager\Exceptions\ConfigurationFileNotFoundException;

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

    /**
     * @test
     */
    public function loaderLoadEnvConfiguration()
    {
        $loader = new ENVConfigurationLoader();
        $configArray = $loader->load([__DIR__ . './config/.env']);
        $this->assertNotEmpty($configArray);
        $this->assertSame('abdeslam', $configArray['username']);
        $this->assertSame(false, $configArray['debug']);
        $this->assertSame('abdeslam_1234', $configArray['password']);
    }
}
