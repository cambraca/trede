<?php

namespace System;

use Cache\Cache;
use Core\Component;
use Core\Hook;

class Settings extends Component {
  /**
   * @const Permanent settings are normally stored in a database.
   * If no provider is available, these are stored as file settings.
   */
  const STORAGE_PERMANENT = 'permanent';
  /**
   * @const File settings are stored in auto-generated files.
   */
  const STORAGE_FILE = 'file';
  /**
   * @const Cache settings are generally stored in memory caching systems.
   * E.g. memcache, redis. If none is available, uses file storage.
   */
  const STORAGE_CACHE = 'cache';
  /**
   * @const Volatile settings are only good for the current process.
   */
  const STORAGE_VOLATILE = 'volatile';

  /**
   * @const String is the simplest type of variable.
   */
  const TYPE_STRING = 'string';
  /**
   * @const Arrays can contain any number of settings.
   */
  const TYPE_ARRAY = 'array';

  public $definitions;

  protected $file_cache = [];
  protected $volatile = [];

  protected function filename($class) {
    return 'settings'
      .DIRECTORY_SEPARATOR.str_replace('\\', '_', $class).'.json';
  }

  /**
   * Clears the file variables in the specified class.
   * Be careful, calling this function deletes the settings file, if any.
   * @param $class
   */
  function clearFileSettings($class) {
    $filename = $this->filename($class);

    if (file_exists($filename))
      unlink($filename);

    if (isset($this->file_cache[$class]))
      unset($this->file_cache[$class]);
  }

  function get($class, $name) {
    $definition = $this->definition($class, $name);
    switch ($definition['storage']) {
      case self::STORAGE_FILE:
        $filename = $this->filename($class);
        if (!isset($this->file_cache[$class]) && file_exists($filename))
          $this->file_cache[$class] = json_decode(file_get_contents($filename), TRUE);

        if (isset($this->file_cache[$class][$name]))
          return $this->file_cache[$class][$name];

        break;
      case self::STORAGE_VOLATILE:
        if (isset($this->volatile[$class][$name]))
          return $this->volatile[$class][$name];

        break;
    }

    if (isset($definition['default']))
      return $definition['default'];
  }

  function set($class, $name, $value) {
    $definition = $this->definition($class, $name);
    if (!$definition)
      throw new \Exception("No settings definition for variable: $name");

    switch ($definition['storage']) {
      case self::STORAGE_FILE:
        $filename = $this->filename($class);
        if (!isset($this->file_cache[$class]) && file_exists($filename))
          $this->file_cache[$class] = json_decode(file_get_contents($filename));

        if (!isset($this->file_cache[$class]) || !is_array($this->file_cache[$class]))
          $this->file_cache[$class] = [];

        $this->file_cache[$class][$name] = $value;

        $dir = pathinfo($filename, PATHINFO_DIRNAME);
        if (!file_exists($dir))
          mkdir(pathinfo($filename, PATHINFO_DIRNAME), 0777, TRUE);
        file_put_contents($filename, json_encode($this->file_cache[$class]));
        break;
      case self::STORAGE_VOLATILE:
        $this->volatile[$class][$name] = $value;
        break;
    }
  }

  private function definition($class, $name) {
    $this->loadDefinitions();

    if (!isset($this->definitions[$class][$name]))
      return;

    return $this->definitions[$class][$name];
  }

  private function loadDefinitions() {
    if ($this->definitions)
      return;

    $this->definitions = Cache::i()->get('settings', 'file');
    if ($this->definitions)
      return;

    $this->definitions = [];
    foreach (implementers('System\\Settings', 'Variables', TRUE) as $class) {
      /* @var Settings\Variables $class */
      list($package, $component) = explode('\\', $class);
      $this->definitions["$package\\$component"] = $class::definitions();
    }

    Cache::i()->set('settings', $this->definitions, 'file');
  }
}
