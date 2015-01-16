<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 1/5/15
 * Time: 3:44 PM
 */

namespace Cache;

use Core\Component;

class FileTest extends \PHPUnit_Framework_TestCase {
  private static $filename;

  public static function setUpBeforeClass() {
    self::$filename = 'cache'
      .DIRECTORY_SEPARATOR.'file'
      .DIRECTORY_SEPARATOR.'file.json';

    //Backup the existing cache file, if any
    if (file_exists(self::$filename))
      rename(self::$filename, self::$filename.'_TEMP');
  }

  public static function tearDownAfterClass() {
    //Restore the backup made in setUpBeforeClass()
    if (file_exists(self::$filename.'_TEMP'))
      rename(self::$filename.'_TEMP', self::$filename);
  }


  function testFileBin() {
    $cache = Cache::i();
    $cache->clear('file');
    $cache->set('test_key', 'test_value', 'file');
    $this->assertEquals('test_value', $cache->get('test_key', 'file'));
  }

  function testInternals() {
    $cache = Cache::i();
    Component::finalizeAll(); //make sure the file gets written
    $this->assertFileExists(self::$filename);
    $data = json_decode(file_get_contents(self::$filename), TRUE);
    $this->assertEquals(['test_key' => 'test_value'], $data);
    $cache->clear('file');
  }
}
