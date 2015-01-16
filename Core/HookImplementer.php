<?php

namespace Core;

/**
 * Interface HookImplementer.
 *
 * Through this interface, the Trede system implements a kind of aspect-oriented
 * programming pattern. Components may declare interfaces that extend this one
 * as their API, allowing other components to implement them.
 *
 * Normally these interfaces only have static functions.
 *
 * Implementers can optionally declare a public $weight integer value, which
 * allows the modification of the order in which a certain hook's implementers
 * is called. If not specified, a value of 0 is assumed.
 *
 * @package Core
 */
interface HookImplementer {
}

interface Alterable extends HookImplementer {
  static function add();
  static function alter();
  static function remove();
}
