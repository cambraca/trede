<?php

namespace Entity\Entity;

use System\Document\Outline;

class Documentation implements Outline {
  static function outline() {
    return [
      'Intro',
      'The basic Entity' => [
        'One',
        'Two',
      ],
      'Storing Entities in a database',
    ];
  }
}
