<?php

namespace KPIs\Category;

class Routes implements \Http\Router\Routes {
  static function add() {
    return [
      'categories' => [
        'intl' => [
          'es' => 'categorias',
        ],
      ]
    ];
  }

  static function remove() {
  }

  static function alter() {
  }

}