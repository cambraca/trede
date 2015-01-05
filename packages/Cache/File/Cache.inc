<?php

namespace Cache\File;

use \Cache\Cache\Bins as Source;

class FileBin implements Source {
  private static $data = [];

  static function add() {
    return [
      'file' => [],
      'components' => [],
    ];
  }

  static function alter($bin) {
  }

  static function get($key, $bin) {
    if (!isset(self::$data[$bin]) && file_exists(self::filename($bin)))
      self::$data[$bin] = json_decode(file_get_contents(self::filename($bin)), TRUE);

    if (is_array(self::$data[$bin]) && isset(self::$data[$bin][$key]))
      return self::$data[$bin][$key];
  }

  /**
   * @todo Only write changes to the file when the request ends.
   */
  static function set($key, $value, $bin) {
    if (!isset(self::$data[$bin]) && file_exists(self::filename($bin)))
      self::$data[$bin] = json_decode(file_get_contents(self::filename($bin)), TRUE);

    if (!isset(self::$data[$bin]))
      self::$data[$bin] = [];

    self::$data[$bin][$key] = $value;

    file_put_contents(self::filename($bin), json_encode(self::$data[$bin]));
  }

  static function clear($bin) {
    if (isset(self::$data[$bin]))
      unset(self::$data[$bin]);

    if (file_exists(self::filename($bin)))
      unlink(self::filename($bin));
  }

  private static function directory() {
    $directory = 'cache'
      .DIRECTORY_SEPARATOR.'file';

    if (!file_exists($directory))
      mkdir($directory, 0777, TRUE);

    return $directory;
  }

  private static function filename($bin) {
    return self::directory()
      .DIRECTORY_SEPARATOR.$bin.'.json';
  }

}