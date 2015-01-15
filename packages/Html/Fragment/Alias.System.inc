<?php

namespace HTML\Fragment;

use System\Alias\Aliases as Source;

class Aliases implements Source {
  static function register() {
    return [
      'twig' => [
        'package' => 'HTML',
        'component' => 'Fragment',
        'method' => 'twig',
      ],
    ];
  }
}
