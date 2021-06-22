<?php

namespace Tests;

use ReflectionClass;
use PHPUnit\Framework\TestCase;
use Abdeslam\Configuration\ConfigurationManager;
use Abdeslam\Configuration\Exceptions\InvalidKeyException;
use Abdeslam\Configuration\Loaders\PHPConfigurationLoader;
use Abdeslam\Configuration\Loaders\JSONConfigurationLoader;
use Abdeslam\Configuration\Contracts\ConfigurationLoaderInterface;
use Abdeslam\Configuration\Exceptions\InvalidKeyExceptionException;
use Abdeslam\Configuration\Exceptions\ConfigurationItemNotFoundException;

/**
 * @coversDefaultClass \Abdeslam\Configuration\Configuration
 */
class ConfigurationManagerTest extends TestCase
{

    /**
     * @test
     */
    public function configurationHasItems()
    {
        $config = $this->createDefaultConfiguration();
        $configItems = $config->all();
        $expected = [
            'app' => 'my-app',
            'credentials' => [
                'username' => 'user1',
                'password' => '1234'
            ]
        ];
        $this->assertNotEmpty($configItems);
        $this->assertSame(
            $expected,
            $configItems
        );
    }

    /**
     * @test
     */
    public function configurationHas()
    {
        $config = $this->createDefaultConfiguration();
        $this->assertTrue($config->has('app'));
        $this->assertFalse($config->has('non_existent_key'));
    }

    /**
     * @test
     */
    public function configurationGet()
    {
        $config = $this->createDefaultConfiguration();
        $this->assertSame(
            $config->get('app'),
            'my-app'
        );
        $this->assertSame(
            $config->get('credentials.username'),
            'user1'
        );
    }


    /**
     * @test
     */
    public function configurationGetNonexistentKey()
    {
        $config = $this->createDefaultConfiguration();
        $this->expectException(ConfigurationItemNotFoundException::class);
        $config->get('non_existent_key');
    }

    /**
     * @test
     */
    public function configurationGetWithDefaultValue()
    {
        $config = $this->createDefaultConfiguration();
        $this->assertSame(
            $config->get('non_existent_key', 'value'),
            'value'
        );
    }

    /**
     * @test
     */
    public function configurationWithMultipleFiles()
    {
        $loader = new PHPConfigurationLoader();
        $config = new ConfigurationManager();
        $config->addLoader(
            $loader,
            [
                __DIR__ . '/config/config.php',
                __DIR__ . '/config/config2.php'
            ]
        );
        $this->assertArrayHasKey(
            'default_local',
            $config->all()
        );
        $this->assertSame(
            'en',
            $config->get('default_local')
        );
    }

    /**
     * @test
     */
    public function configurationMergeArray()
    {
        $loader = new PHPConfigurationLoader();
        $config = new ConfigurationManager();
        $config->addLoader(
            $loader,
            [
                __DIR__ . '/config/config.php',
                __DIR__ . '/config/config2.php'
            ]
        );
        $config->merge(['foo' => 'bar']);
        $this->assertTrue($config->has('foo'));
        $this->assertSame('bar', $config->get('foo'));
    }

    /**
     * @test
     */
    public function configurationMergeConfigurationObject()
    {
        $loader = new PHPConfigurationLoader();
        $config = new ConfigurationManager();
        $config->addLoader(
            $loader,
            __DIR__ . '/config/config2.php'
        );
        $config2 = $this->createDefaultConfiguration();
        $config->merge($config2);
        $this->assertTrue($config->has('default_local'));
        $this->assertSame('en', $config->get('default_local'));
    }

    /**
     * @test
     */
    public function configurationSet()
    {
        $config = $this->createDefaultConfiguration();
        $config->set('foo', 'bar');
        $this->assertTrue($config->has('foo'));
        $this->assertSame('bar', $config->get('foo'));
    }

    /**
     * @test
     */
    public function configurationReset()
    {
        $config = $this->createDefaultConfiguration();
        $config->setKeySeparator('#');
        $config->reset();
        $this->assertEmpty($config->all());
        $this->assertEmpty($config->getLoaders());
        $this->assertSame(
            '.',
            $config->getKeySeparator()
        );
    }

    /**
     * @test
     */
    public function configurationGetLoader()
    {
        $config = $this->createDefaultConfiguration();
        $this->assertInstanceOf(
            PHPConfigurationLoader::class,
            $config->getLoader(PHPConfigurationLoader::class)
        );
        $this->assertNull($config->getLoader('non_existent_loader'));
    }


    /**
     * @test
     */
    public function configurationGetLoaders()
    {
        $config = $this->createDefaultConfiguration();
        $this->assertNotNull($config->getLoaders());
        $this->assertArrayHasKey(
            PHPConfigurationLoader::class,
            $config->getLoaders()
        );
    }

    /**
     * @test
     */
    public function configurationAddLoader()
    {
        $config = $this->createDefaultConfiguration();
        $JSONLoader = new JSONConfigurationLoader();
        $config->addLoader($JSONLoader, __DIR__ .'/config/config.json');
        $this->assertArrayHasKey(
            JSONConfigurationLoader::class,
            $config->getLoaders()
        );
        $this->assertTrue($config->has('debug'));
        $this->assertTrue($config->get('debug'));
        $this->assertTrue($config->has('author.name'));
        $this->assertSame(
            'abdeslam',
            $config->get('author.name')
        );
        $newLoader = new class implements ConfigurationLoaderInterface {
            public function load($filename): array
            {
                return ["hello" => "world"];
            }
        };
        $config->addLoader($newLoader, 'foo');
        $this->assertTrue($config->has('hello'));
        $this->assertSame(
            $config->get('hello'),
            'world'
        );
    }

    /**
     * @test
     */
    public function configurationSetKeySeparator()
    {
        $config = $this->createDefaultConfiguration();
        $config->setKeySeparator('_');
        $this->assertSame(
            '_',
            $config->getKeySeparator()
        );
        $this->assertSame(
            'user1',
            $config->get('credentials_username')
        );
        $this->assertFalse($config->has('credentials.username'));
    }

    /**
     * @test
     */
    public function configurationResolveKey()
    {
        $config = $this->createDefaultConfiguration();
        $reflect = new ReflectionClass($config);
        $method = $reflect->getMethod('resolveKey');
        $method->setAccessible(true);
        $this->assertSame(
            ['key1', 'key2'],
            $method->invokeArgs($config, ['key1.key2'])
        );
        $this->assertSame(
            ['key1'],
            $method->invokeArgs($config, ['key1'])
        );

        $this->expectException(InvalidKeyException::class);
        $method->invokeArgs($config, [1]);
        $method->setAccessible(false);
    }

    /**
     * returns a configuration instance with a PHPConfigurationLoader
     *
     * @codeCoverageIgnore
     * @return Configuration
     */
    private function createDefaultConfiguration(): ConfigurationManager
    {
        $loader = new PHPConfigurationLoader();
        $config = new ConfigurationManager();
        $config->addLoader(
            $loader,
            __DIR__ . '/config/config.php'
        );
        return $config;
    }
}
