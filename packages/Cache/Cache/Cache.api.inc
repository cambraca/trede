<?php

namespace Cache\Cache;

use \Core\HookImplementer;

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
