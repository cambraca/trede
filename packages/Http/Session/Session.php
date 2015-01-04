<?php

namespace Http;

use Core\Component;

class Session extends Component implements \ArrayAccess {
  const SESSION_KEY = 'fsess';

  public function offsetExists($offset) {
    return isset($_SESSION[self::SESSION_KEY][$offset]);
  }

  public function offsetGet($offset) {
    return $_SESSION[self::SESSION_KEY][$offset];
  }

  public function offsetSet($offset, $value) {
    $_SESSION[self::SESSION_KEY][$offset] = $value;
  }

  public function offsetUnset($offset) {
    unset($_SESSION[self::SESSION_KEY][$offset]);
  }
}