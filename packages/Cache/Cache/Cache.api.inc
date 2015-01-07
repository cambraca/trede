<?php

namespace Cache\Cache;

use \Core\HookImplementer;

interface Bins extends HookImplementer {
  /**
   * Adds new bin types. Each array entry is assigned a "storage" key with the
   * implementer's class name, unless explicitly specified.
   * @return array|NULL
   */
  static function add();

  /**
   * Alters properties of any bin. For example, altering the storage value for
   * the "default" bin to store the variables in Memcache.
   * A call for each existing bin is made to every implementer of Bins. If any
   * bin properties are not returned as keys in the array, they will not be
   * modified.
   * @param $bin
   * @return array|NULL
   */
  static function alter($bin);

  /**
   * Get a cached value from the specified bin.
   * @param $key
   * @param $bin
   * @return mixed
   */
  static function get($key, $bin);

  /**
   * Set a cache value.
   * @param $key
   * @param $value
   * @param $bin
   * @return mixed
   */
  static function set($key, $value, $bin);

  /**
   * Clear all cached values.
   */
  static function clear($bin);
}
