<?php

namespace Html\Fragment;

use Cache\Cache\Clear;

class TwigCache implements Clear {
  static function clear() {
    twig()->clearCacheFiles();
    twig()->clearTemplateCache();
  }
}
