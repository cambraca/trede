<?php

namespace TestPackage\TestComponent;

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
