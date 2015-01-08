<?php

namespace Html\Fragment;

use System\Alias\Aliases as Source;

class Aliases implements Source {
  static function register() {
    return [
      'twig' => [
        'package' => 'Html',
        'component' => 'Fragment',
        'method' => 'twig',
      ],
    ];
  }
}
