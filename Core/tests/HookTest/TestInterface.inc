<?php

namespace TestPackage\TestComponent;
use \Core\HookImplementer;

abstract class TestInterface extends HookImplementer {
  static function doSomething($variable) {}
}