<?php

namespace Cli\Cli;

use \Core\HookImplementer;

interface Commands extends HookImplementer {
  /**
   * Adds CLI commands.
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
}
