<?php

namespace Database\Connection;

use System\Alias\Aliases as Source;

class Aliases implements Source {
  static function register() {
    return [
      'query' => [
        'package' => 'Database',
        'component' => 'Connection',
        'method' => 'query',
      ],
      'conn' => [
        'package' => 'Database',
        'component' => 'Connection',
        'method' => 'conn',
      ]
    ];
  }

}