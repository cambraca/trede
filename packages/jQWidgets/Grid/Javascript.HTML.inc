<?php

namespace jQWidgets\Grid;

use jQWidgets\jQWidgets;

class Javascript implements \HTML\Javascript\Includes {

  static function add() {
    $path = location('jQWidgets\\jQWidgets', jQWidgets::LIBRARY_PATH.'/jqxgrid.js');

    return [
      'jQWidgets grid' => [
        'type' => \HTML\Javascript\Includes::TYPE_FILE,
        'path' => $path,
        'after' => 'jQWidgets core',
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
