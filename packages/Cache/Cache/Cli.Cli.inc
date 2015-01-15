<?php

namespace Cache\Cache;

use Cache\Cache;
use Cli\Cli;
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
    switch ($command) {
      case 'clear-cache':
        $cache = Cache::i();

        if (count($arguments) > 0) {
          $bin = array_shift($arguments);
          $cache->clear($bin == 'all' ? NULL : $bin);
          return $bin == 'all' ? 'Cleared all caches' : 'Cleared cache for bin: ' . $bin;
        }

        //Present a menu to the user.
        $menu = [];
        foreach ($cache->getBins() as $bin) {
          $menu[$bin] = [
            'label' => $bin,
            'callback' => [[$cache, 'clear'], [$bin]]
          ];
        }

        $external = [];
        foreach (implementers('Cache\\Cache', 'External', TRUE) as $implementer) {
          /**
           * @var Cache\External $implementer
           */
          $external[$implementer] = [
            'label' => $implementer::name(),
            'callback' => [[$implementer, 'clear']]
          ];
        }
        if ($external) {
          $menu['external'] = ['label' => 'EXTERNAL', 'children' => $external];
        }

        $menu['all'] = ['label' => 'ALL', 'callback' => [[$cache, 'clear']]];

        $success = Cli::i()->standalone('menu', $menu);

        return $success ? 'Cache cleared successfully' : 'There was a problem clearing cache';
    }
  }

  static function help($command) {
    return <<<EOS
Clears the specified cache, or "all" if specified.
Usage:
  $ php index.php clear-cache all
  $ php index.php clear-cache my_cache_bin
  $ php index.php clear-cache
The last usage shown presents a menu in which you can select a cache bin.
EOS;
  }
}
