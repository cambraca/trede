<?php

namespace HTML\Fragment;

use Cache\Cache\External;

class TwigCache implements External {
  static function name() {
    return 'twig';
  }

  static function clear() {
    twig()->clearCacheFiles();
    twig()->clearTemplateCache();
  }
}
