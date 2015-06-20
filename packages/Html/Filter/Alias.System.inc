<?php

namespace HTML\Filter;

use System\Alias\Aliases as Source;

class Aliases implements Source {
  static function register() {
    return [
      'filter' => [
        'package' => 'HTML',
        'component' => 'Filter',
        'method' => 'filter',
      ],
//      'twig' => [
//        'package' => 'HTML',
//        'component' => 'Twig',
//        'method' => 'twig',
//      ],
    ];
  }
}
