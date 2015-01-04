<?php

namespace Database\Connection;

use System\Settings;
use System\Settings\Variables;

class DBSettings implements Variables {
  static function definitions() {
    return [
      'dbname' => [
        'label' => 'Database name',
        'storage' => Settings::STORAGE_FILE,
        'type' => Settings::TYPE_STRING,
      ],
      'host' => [
        'label' => 'Host',
        'storage' => Settings::STORAGE_FILE,
        'type' => Settings::TYPE_STRING,
        'default' => 'localhost',
      ],
      'user' => [
        'label' => 'User',
        'storage' => Settings::STORAGE_FILE,
        'type' => Settings::TYPE_STRING,
      ],
      'password' => [
        'label' => 'Password',
        'storage' => Settings::STORAGE_FILE,
        'type' => Settings::TYPE_STRING,
//        'type' => Settings::TYPE_PASSWORD, TODO: make this work
      ],
    ];
  }
}