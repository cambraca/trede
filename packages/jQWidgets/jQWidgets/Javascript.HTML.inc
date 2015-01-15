<?php

namespace jQWidgets\jQWidgets;

use jQWidgets\jQWidgets;

class Javascript implements \HTML\Javascript\Includes {

  static function add() {
    $path = location('jQWidgets\\jQWidgets', jQWidgets::LIBRARY_PATH.'/jqxcore.js');

    return [
      'jQWidgets core' => [
        'type' => \HTML\Javascript\Includes::TYPE_FILE,
        'path' => $path,
        'after' => 'jQuery',
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
