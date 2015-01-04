<?php

namespace TestPackage\TestComponent;

use System\Settings;
use System\Settings\Variables;

class Options implements Variables {
  static function definitions() {
    return [
      'test_volatile_setting' => [
        'label' => 'Volatile setting',
        'storage' => Settings::STORAGE_VOLATILE,
        'type' => Settings::TYPE_STRING,
      ],
      'test_default_setting' => [
        'label' => 'Volatile setting',
        'storage' => Settings::STORAGE_VOLATILE,
        'type' => Settings::TYPE_STRING,
        'default' => 'test_default_value',
      ],
      'test_file_setting' => [
        'label' => 'File setting',
        'storage' => Settings::STORAGE_FILE,
        'type' => Settings::TYPE_STRING,
      ],
    ];
  }
}