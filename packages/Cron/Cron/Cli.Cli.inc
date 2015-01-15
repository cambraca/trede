<?php

namespace Cron\Cron;

use Cli\Cli\Commands;

class CliRunner implements Commands {
  static function add() {
    return [
      'cron' => [
        'class' => 'Cron\\Cron\\CliRunner',
      ],
    ];
  }

  static function remove() {
  }

  static function alter() {
  }

  static function run($command, $arguments) {
    //TEMP: testing the db
    print_r(
      conn()->fetchAll(
        query()
          ->select('nid', 'title')
          ->from('node', 'n')
          ->setMaxResults(5)
      )
    );

    print_r($arguments);
    return 'Running CRON with above args';
  }

  static function help($command) {
    return <<<EOS
Runs the cron.
EOS;
  }
}