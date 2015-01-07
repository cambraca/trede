<?php

namespace TestPackage\TestComponent;

use \Core\HookImplementer;

interface TestInterface extends HookImplementer {
  static function doSomething($variable);
}