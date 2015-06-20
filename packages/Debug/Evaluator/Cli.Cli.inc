<?php

namespace Debug\Evaluator;

class Evaluate implements \Cli\Cli\Commands {
  /**
   *
   * @param string $command
   * @param array $arguments
   * @return mixed
   */
  static function run($command, $arguments) {
    switch ($command) {
      case 'evaluate':
        if (!count($arguments))
          return 'No code provided.';

        $ret = '';
        foreach ($arguments as $argument) {
          $response = eval($argument);

          if (is_array($response))
            $response = print_r($response);
          //TODO: handle more return types (objects, etc)

          $ret .= ($ret ? PHP_EOL : '') . $response;
        }
        return $ret ? $ret : 'No output returned by code.';
    }
  }

  /**
   * Provide help text to be shown in the console. Triggered by the "help"
   * command.
   * @param $command
   * @return mixed
   */
  static function help($command) {
    return 'TO-DO';
  }

  static function add() {
    return [
      'evaluate' => [
        'class' => 'Debug\\Evaluator\\Evaluate',
      ],
    ];
  }

  static function alter() {
  }

  static function remove() {
  }

}
