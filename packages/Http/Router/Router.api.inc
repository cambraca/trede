<?php

namespace Http\Router;
use \Core\HookImplementer;

interface Routes extends HookImplementer {
  static function add($path, $options);
  static function remove($path);
  static function alter($path, $options);
}
