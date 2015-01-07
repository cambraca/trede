<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 1/5/15
 * Time: 2:46 PM
 */

namespace Cache;

use Core\Component;
use CacheTestPackage\CacheTestComponent\Bins;

include_once 'packages/Cache/Cache/Cache.api.inc';

class CacheTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var Cache
   */
  private static $cache;

  static function setUpBeforeClass() {
    Component::resetAll();
    Component::rebuildDefinitions(FALSE, [
      'Cache\\Cache' => [
        'api' => [
          'Bins' => [
            'Cache\\File\\FileBin',
            'CacheTestPackage\\CacheTestComponent\\Bins',
            'CacheTestPackage\\CacheTestComponent\\BinsAlterer',
          ]
        ],
      ],
      'Cache\\File' => [],
      'CacheTestPackage\\CacheTestComponent' => [],
    ]);
    self::$cache = Cache::i();
  }

  static function tearDownAfterClass() {
    Component::resetAll();
    Component::rebuildDefinitions();
  }

  protected function setUp() {
    //delete test cache on every test, to be sure there is no interference
    Bins::clear('test_bin');
    Bins::clear('test_bin_internal');
    Bins::clear('test_bin_to_alter');
  }

  function testSimpleCacheEntry() {
    self::$cache->set('my_key', 'my_value');
    $this->assertEquals('my_value', self::$cache->get('my_key'));
    self::$cache->set('my_other_key', 'my_other_value');
    $this->assertEquals('my_other_value', self::$cache->get('my_other_key'));
    self::$cache->set('my_key', 'my_third_value');
    $this->assertEquals('my_third_value', self::$cache->get('my_key'));
  }

  function testNonExistingBin() {
    self::$cache->set('my_key', 'my_value', 'bogus_bin');
    $this->assertEquals('my_value', self::$cache->get('my_key', 'bogus_bin'));
  }

  function testCustomBin() {
    self::$cache->set('my_key', 'my_value', 'default');
    self::$cache->set('my_key', 'my_other_value', 'test_bin');
    $this->assertEquals('my_value', self::$cache->get('my_key', 'default'));
    $this->assertEquals('my_other_value', self::$cache->get('my_key', 'test_bin'));
  }

  function testCustomBinWithSameStorage() {
    self::$cache->set('my_key', 'my_value', 'test_bin_internal');
    self::$cache->set('my_key', 'my_other_value', 'default');
    //value should get replaced
    $this->assertEquals('my_other_value', self::$cache->get('my_key', 'test_bin_internal'));
    $this->assertEquals('my_other_value', self::$cache->get('my_key', 'default'));
  }

  function testAlterBin() {
    self::$cache->set('my_key', 'my_value', 'test_bin_to_alter');
    self::$cache->set('my_key', 'my_other_value', 'default');
    //bin storage should be altered to "internal"
    $this->assertEquals('my_other_value', self::$cache->get('my_key', 'test_bin_to_alter'));
    $this->assertEquals('my_other_value', self::$cache->get('my_key', 'default'));
  }

  function testClearCache() {
    //test clearing only "test_bin"
    self::$cache->set('my_key', 'my_value', 'default');
    self::$cache->set('my_key', 'my_other_value', 'test_bin');
    self::$cache->clear('test_bin');
    $this->assertEquals('my_value', self::$cache->get('my_key', 'default'));
    $this->assertNull(self::$cache->get('my_key', 'test_bin'));

    //test clearing only "default"
    self::$cache->set('my_key', 'my_value', 'default');
    self::$cache->set('my_key', 'my_other_value', 'test_bin');
    self::$cache->clear('default');
    $this->assertNull(self::$cache->get('my_key', 'default'));
    $this->assertEquals('my_other_value', self::$cache->get('my_key', 'test_bin'));

    //test clearing all bins
    self::$cache->set('my_key', 'my_value', 'default');
    self::$cache->set('my_key', 'my_other_value', 'test_bin');
    self::$cache->clear();
    $this->assertNull(self::$cache->get('my_key', 'default'));
    $this->assertNull(self::$cache->get('my_key', 'test_bin'));
  }
}

namespace CacheTestPackage\CacheTestComponent;

use Cache\Cache\Bins as Source;

class Bins implements Source {
  private static $data = [];

  static function add() {
    return [
      'test_bin' => [
      ],
      'test_bin_internal' => [
        'storage' => 'internal',
      ],
      'test_bin_to_alter' => [
      ],
    ];
  }

  static function alter($bin) {
  }

  static function get($key, $bin) {
    if (isset(self::$data[$bin][$key]))
      return self::$data[$bin][$key];
  }

  static function set($key, $value, $bin) {
    if (!isset(self::$data[$bin]))
      self::$data[$bin] = [];

    self::$data[$bin][$key] = $value;
  }

  static function clear($bin) {
    self::$data[$bin] = [];
  }
}

class BinsAlterer implements Source {
  static function add() {
    return [];
  }

  static function alter($bin) {
    if ($bin == 'test_bin_to_alter')
      return ['storage' => 'internal'];
  }

  static function get($key, $bin) {
  }

  static function set($key, $value, $bin) {
  }

  static function clear($bin) {
  }

}
