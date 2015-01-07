<?php

namespace Cache\Cache;

use Cli\Cli\Commands;

class ClearCache implements Commands {
  static function add() {
    return [
      'clear-cache' => [
        'class' => 'Cache\\Cache\\ClearCache',
      ],
    ];
  }

  static function remove() {
  }

  static function alter() {
  }

  static function run($command, $arguments) {
    if (count($arguments) > 0) {
      \Cache\Cache::i()->clear($arguments[0]);
      return 'Cleared cache for bin: ' . $arguments[0];
    }

    \Cache\Cache::i()->clear();
    return 'Cleared all caches';
  }
}