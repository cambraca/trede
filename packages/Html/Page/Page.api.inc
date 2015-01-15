<?php

namespace HTML\Page;
use \Core\HookImplementer;

interface FilterOutput extends HookImplementer {
  static function filter($html);
}

interface Head extends HookImplementer {
  static function append();
}
