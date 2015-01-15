<?php

namespace HTML\Minimize;
use HTML\Page\FilterOutput as Source;

class FilterOutput implements Source {
  static function weight() {return 0;}
  /**
   * @todo This is not a good implementation. Only a good example of pointcuts.
   */
  static function filter($html) {
    return preg_replace('/\>\s+\</', '><', $html);
  }
}
