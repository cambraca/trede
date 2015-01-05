<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 1/5/15
 * Time: 3:44 PM
 */

namespace Cache;

class FileTest extends \PHPUnit_Framework_TestCase {
  function testFileBin() {
    $cache = Cache::i();
    $cache->clear('file');
    $cache->set('test_key', 'test_value', 'file');
    $this->assertEquals('test_value', $cache->get('test_key', 'file'));
  }

  function testInternals() {
    $cache = Cache::i();
    $filename = 'cache'
      .DIRECTORY_SEPARATOR.'file'
      .DIRECTORY_SEPARATOR.'file.json';
    $this->assertFileExists($filename);
    $data = json_decode(file_get_contents($filename), TRUE);
    $this->assertEquals(['test_key' => 'test_value'], $data);
    $cache->clear('file');
  }
}
