<?php

namespace HTML\Javascript;
use HTML\Javascript;

class JSIncludes implements \HTML\Page\Head {
  static function append() {
    $includes = Javascript::i()->getIncludes();

    $ret = [];
    foreach ($includes as $id => $include) {
      $ret[$id] = '<script src="' . $include['path'] . '"></script>';
    }

    return $ret;
  }
}
