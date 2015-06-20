<?php

namespace System\Document;
use System\Settings;
use System\Settings\Variables;

class Options implements Variables {
  static function definitions() {
    return [
      'url_prefix' => [
        'label' => 'URL Prefix',
        'storage' => Settings::STORAGE_FILE,
        'type' => Settings::TYPE_STRING,
      ],
    ];
  }
}
