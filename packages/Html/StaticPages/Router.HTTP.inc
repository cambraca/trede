<?php

namespace HTML\StaticPages;

class Routes implements \HTTP\Router\Routes {
  static function add() {
    return \HTML\StaticPages::i()->loadRoutes();
  }

  static function remove() {
    // TODO: Implement remove() method.
  }

  static function alter() {
    // TODO: Implement alter() method.
  }

}
