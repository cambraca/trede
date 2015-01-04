<?php

namespace Google\Analytics;
use System\Settings;
use System\Settings\Variables;

class Options implements Variables {
  static function definitions() {
    return [
      'tracking_id' => [
        'label' => 'Tracking ID',
        'storage' => Settings::STORAGE_FILE,
        'type' => Settings::TYPE_STRING,
      ],
    ];
  }
}