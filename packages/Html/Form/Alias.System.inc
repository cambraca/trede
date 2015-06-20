<?php

namespace HTML\Form;

use System\Alias\Aliases as Source;

class Aliases implements Source {
  static function register() {
    return [
      'form' => [
        'package' => 'HTML',
        'component' => 'Form',
        'method' => 'form',
      ],
    ];
  }
}
