<?php

namespace HTML\Twig;

use Cache\Cache\External;
use HTML\Twig;

class TwigCache implements External {
  static function name() {
    return 'twig';
  }

  static function clear() {
    Twig::i()->twig()->clearCacheFiles();
    Twig::i()->twig()->clearTemplateCache();
  }
}
