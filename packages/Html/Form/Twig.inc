<?php

namespace HTML\Form;

class Paths implements \HTML\Twig\Paths {
  static function add() {
    return [
      'vendor/symfony/twig-bridge/Symfony/Bridge/Twig/Resources/views/Form',
    ];
  }

  static function alter() {
  }

  static function remove() {
  }

}
