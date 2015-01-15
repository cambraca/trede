<?php

namespace jQuery\jQuery;

class Includes implements \HTML\Javascript\Includes {

  static function add() {
    if (\Core\Bootstrap::isDevelopmentMode())
      $path = location('jQuery\\jQuery', 'js/jquery-1.11.2.js');
    else
      $path = location('jQuery\\jQuery', 'js/jquery-1.11.2.min.js');

    return [
      'jQuery' => [
        'type' => \HTML\Javascript\Includes::TYPE_FILE,
        'path' => $path,
      ],
    ];
  }

  static function alter() {
    // TODO: Implement alter() method.
  }

  static function remove() {
    // TODO: Implement remove() method.
  }

}
