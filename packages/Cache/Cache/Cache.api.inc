<?php

namespace Cache\Cache;

use \Core\HookImplementer;

//interface Bins extends HookImplementer {
//  /**
//   * Adds new bin types. Each array entry is assigned a "storage" key with the
//   * implementer's class name, unless explicitly specified.
//   * @return array|NULL
//   */
//  static function add();
//
//  /**
//   * Alters properties of any bin. For example, altering the storage value for
//   * the "default" bin to store the variables in Memcache.
//   * A call for each existing bin is made to every implementer of Bins. If any
//   * bin properties are not returned as keys in the array, they will not be
//   * modified.
//   * @param string $bin
//   * @return array|NULL
//   */
//  static function alter($bin);
//
//  /**
//   * Get a cached value from the specified bin.
//   * @param string $key
//   * @param string $bin
//   * @return mixed
//   */
//  static function get($key, $bin);
//
//  /**
//   * Set a cache value.
//   * @param string $key
//   * @param mixed $value
//   * @param string $bin
//   */
//  static function set($key, $value, $bin);
//
//  /**
//   * Clear all cached values for the specified bin.
//   * @param string $bin
//   * @return bool TRUE if successful
//   */
//  static function clear($bin);
//}

interface External extends HookImplementer {
  /**
   * Returns the name of the external cache.
   * @return string
   */
  static function name();

  /**
   * Clears an external cache system.
   * Implementers are called only when Cache::i()->clear() is called without
   * parameters.
   */
  static function clear();
}