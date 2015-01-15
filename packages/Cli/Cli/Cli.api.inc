<?php

namespace Cli\Cli;

use \Core\HookImplementer;

interface Commands extends HookImplementer {
  /**
   * Adds CLI commands. Name cannot be "all".
   * @return array
   * E.g. [
   *   'command1' => [
   *     'class' => 'MyPackage\\MyComponent\\MyCommandsImplementer',
   *   ],
   *   'command2' => [
   *     'class' => 'MyPackage\\MyComponent\\MyCommandsImplementer',
   *   ],
   * ]
   */
  static function add();
  static function alter();
  static function remove();

  /**
   *
   * @param string $command
   * @param array $arguments
   * @return mixed
   */
  static function run($command, $arguments);

  /**
   * Provide help text to be shown in the console. Triggered by the "help"
   * command.
   * @param $command
   * @return mixed
   */
  static function help($command);
}

interface Standalone extends HookImplementer {
  static function run($operation, $arguments);
}