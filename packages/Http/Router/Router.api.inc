<?php

namespace HTTP\Router;

use \Core\HookImplementer;

interface Routes extends HookImplementer {
  static function add();
  static function remove();
  static function alter();
}
