<?php

/**
* @author Abdeslam Gacemi <abdobling@gmail.com>
*/

namespace Tests;

use ReflectionClass;
use PHPUnit\Framework\TestCase;
use Abdeslam\ConfigurationManager\ConfigurationManager;
use Abdeslam\ConfigurationManager\Exceptions\InvalidKeyException;
use Abdeslam\ConfigurationManager\Loaders\PHPConfigurationLoader;
use Abdeslam\ConfigurationManager\Loaders\JSONConfigurationLoader;
use Abdeslam\ConfigurationManager\Contracts\ConfigurationLoaderInterface;
use Abdeslam\ConfigurationManager\Exceptions\ConfigurationItemNotFoundException;

/**
 * @coversDefaultClass \Abdeslam\ConfigurationManager\Configuration
 */
class ConfigurationManagerTest extends TestCase
{

    /**
     * @test
     */
    public function configurationHasItems()
    {
        $manager = $this->createDefaultConfiguration();
        $manager->load();
        $managerItems = $manager->all();
        $expected = [
            'app' => 'my-app',
            'credentials' => [
                'username' => 'user1',
                'password' => '1234'
            ]
        ];
        $this->assertNotEmpty($managerItems);
        $this->assertSame(
            $expected,
            $managerItems
        );
    }

    /**
     * @test
     */
    public function configurationHas()
    {
        $manager = $this->createDefaultConfiguration();
        $manager->load();
        $manager->all();
        $this->assertTrue($manager->has('app'));
        $this->assertFalse($manager->has('non_existent_key'));
    }

    /**
     * @test
     */
    public function configurationGet()
    {
        $manager = $this->createDefaultConfiguration();
        $manager->load();
        $this->assertSame(
            $manager->get('app'),
            'my-app'
        );
        $this->assertSame(
            $manager->get('credentials.username'),
            'user1'
        );
    }


    /**
     * @test
     */
    public function configurationGetNonexistentKey()
    {
        $manager = $this->createDefaultConfiguration();
        $manager->load();
        $this->expectException(ConfigurationItemNotFoundException::class);
        $manager->get('non_existent_key');
    }

    /**
     * @test
     */
    public function configurationGetWithDefaultValue()
    {
        $manager = $this->createDefaultConfiguration();
        $manager->load();
        $this->assertSame(
            $manager->get('non_existent_key', 'value'),
            'value'
        );
    }

    /**
     * @test
     */
    public function configurationWithMultipleFiles()
    {
        $loader = new PHPConfigurationLoader();
        $manager = new ConfigurationManager();
        $manager->addLoader(
            $loader,
            __DIR__ . '/config/config.php',
            __DIR__ . '/config/config2.php'
        );
        $manager->load();
        $this->assertArrayHasKey(
            'default_local',
            $manager->all()
        );
        $this->assertSame(
            'en',
            $manager->get('default_local')
        );
    }

    /**
     * @test
     */
    public function configurationMergeArray()
    {
        $loader = new PHPConfigurationLoader();
        $manager = new ConfigurationManager();
        $manager->addLoader(
            $loader,
            __DIR__ . '/config/config.php',
            __DIR__ . '/config/config2.php'
        );
        $manager->load();
        $manager->merge(['foo' => 'bar']);
        $this->assertTrue($manager->has('foo'));
        $this->assertSame('bar', $manager->get('foo'));
    }

    /**
     * @test
     */
    public function configurationMergeConfigurationObject()
    {
        $loader = new PHPConfigurationLoader();
        $manager = new ConfigurationManager();
        $manager->addLoader(
            $loader,
            __DIR__ . '/config/config2.php'
        );
        $manager->load();
        $manager2 = $this->createDefaultConfiguration();
        $manager->load();
        $manager->merge($manager2);
        $this->assertTrue($manager->has('default_local'));
        $this->assertSame('en', $manager->get('default_local'));
    }

    /**
     * @test
     */
    public function configurationSet()
    {
        $manager = $this->createDefaultConfiguration();
        $manager->load();
        $manager->set('foo', 'bar');
        $this->assertTrue($manager->has('foo'));
        $this->assertSame('bar', $manager->get('foo'));
    }

    /**
     * @test
     */
    public function configurationRemove()
    {
        $manager = $this->createDefaultConfiguration();
        $manager->load();
        $manager->remove('app');
        $this->assertFalse($manager->has('app'));
    }

    /**
     * @test
     */
    public function configurationReset()
    {
        $manager = $this->createDefaultConfiguration();
        $manager->load();
        $manager->setKeySeparator('#');
        $manager->reset();
        $this->assertEmpty($manager->all());
        $this->assertEmpty($manager->getLoaders());
        $this->assertSame(
            '.',
            $manager->getKeySeparator()
        );
    }

    /**
     * @test
     */
    public function configurationGetLoader()
    {
        $manager = $this->createDefaultConfiguration();
        $this->assertInstanceOf(
            PHPConfigurationLoader::class,
            $manager->getLoader(PHPConfigurationLoader::class)
        );
        $this->assertNull($manager->getLoader('non_existent_loader'));
    }

    /**
     * @test
     */
    public function configurationHasLoader()
    {
        $manager = $this->createDefaultConfiguration();
        $this->assertTrue(
            $manager->hasLoader(PHPConfigurationLoader::class)
        );
    }

    /**
     * @test
     */
    public function configurationGetLoaders()
    {
        $manager = $this->createDefaultConfiguration();
        $this->assertNotNull($manager->getLoaders());
        $this->assertSame(
            [PHPConfigurationLoader::class],
            array_keys($manager->getLoaders())
        );
    }

    /**
     * @test
     */
    public function configurationAddLoader()
    {
        $manager = $this->createDefaultConfiguration();
        $JSONLoader = new JSONConfigurationLoader();
        $manager->addLoader($JSONLoader, __DIR__ .'/config/config.json');
        $manager->load();
        $this->assertArrayHasKey(
            JSONConfigurationLoader::class,
            $manager->getLoaders()
        );
        $this->assertTrue($manager->has('debug'));
        $this->assertTrue($manager->get('debug'));
        $this->assertTrue($manager->has('author.name'));
        $this->assertSame(
            'abdeslam',
            $manager->get('author.name')
        );
        // test with a custom loader
        $newLoader = new class implements ConfigurationLoaderInterface {
            public function load(array $filename): array
            {
                return ["hello" => "world"];
            }
        };
        $manager->addLoader($newLoader, 'foo');
        $manager->load();
        $this->assertTrue($manager->has('hello'));
        $this->assertSame(
            $manager->get('hello'),
            'world'
        );
    }

    /**
     * @test
     */
    public function configurationSetKeySeparator()
    {
        $manager = $this->createDefaultConfiguration();
        $manager->load();
        $manager->setKeySeparator('_');
        $this->assertSame(
            '_',
            $manager->getKeySeparator()
        );
        $this->assertSame(
            'user1',
            $manager->get('credentials_username')
        );
        $this->assertFalse($manager->has('credentials.username'));
    }

    /**
     * @test
     */
    public function configurationResolveKey()
    {
        $manager = $this->createDefaultConfiguration();
        $reflect = new ReflectionClass($manager);
        $method = $reflect->getMethod('resolveKey');
        $method->setAccessible(true);
        $this->assertSame(
            ['key1', 'key2'],
            $method->invokeArgs($manager, ['key1.key2'])
        );
        $this->assertSame(
            ['key1'],
            $method->invokeArgs($manager, ['key1'])
        );

        $this->expectException(InvalidKeyException::class);
        $method->invokeArgs($manager, [1]);
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
        $manager = new ConfigurationManager();
        $manager->addLoader(
            $loader,
            __DIR__ . '/config/config.php'
        );
        return $manager;
    }
}
