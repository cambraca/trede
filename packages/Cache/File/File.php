<?php

namespace Cache;

class File extends Cache {
  private static $data = [];

  protected static function bins() {
    return [
      'file' => ['storage' => get_class()],
    ];
  }

  function get($key, $bin = 'default') {
    if (!isset(self::$data[$bin]) && file_exists(self::filename($bin)))
      self::$data[$bin] = json_decode(file_get_contents(self::filename($bin)), TRUE);

    if (is_array(self::$data[$bin]) && isset(self::$data[$bin][$key]))
      return self::$data[$bin][$key];
  }

  /**
   * @todo Only write changes to the file when the request ends.
   */
  function set($key, $value, $bin = 'file') {
    if (!isset(self::$data[$bin]) && file_exists(self::filename($bin)))
      self::$data[$bin] = json_decode(file_get_contents(self::filename($bin)), TRUE);

    if (!isset(self::$data[$bin]))
      self::$data[$bin] = [];

    self::$data[$bin][$key] = $value;

    file_put_contents(self::filename($bin), json_encode(self::$data[$bin]));
  }

  function clear($bin = NULL) {
    //TODO: implement $bin==NULL case (or get rid of it!)
    if (isset(self::$data[$bin]))
      unset(self::$data[$bin]);

    if (file_exists(self::filename($bin)))
      unlink(self::filename($bin));

    return TRUE;
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