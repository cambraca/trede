<?php

namespace System\Settings;
use \Core\HookImplementer;

interface StorageType extends HookImplementer {
  static function register($storage_type, $class);
}

interface Variables extends HookImplementer {
  /**
   * @return array
   */
  static function definitions();
}