<?php

namespace Cli;

use Core\Component;

class Cli extends Component {
  protected $commands = [];

  protected function loadCommands() {
    foreach (implementers('Cli\\Cli', 'Commands') as $command) {
      /* @var Cli\Commands $command */
//      foreach ($command::add() as $key => $to_add) {
//        $this->commands[$key] = $to_add;
//      }
      $this->commands = array_merge($this->commands, $command::add());
    }
  }

  function run() {
    global $argc, $argv;
    if ($argc < 2)
      return 'No command specified.';

    $this->loadCommands();

    $arguments = $argv;
    array_shift($arguments); //generally, 'index.php'
    $command = array_shift($arguments);

    if (!isset($this->commands[$command]))
      return "Command unknown or deleted: $command";

    print "Running CLI command: $command\n";

    /** @var Cli\Commands $class */
    $class = $this->commands[$command]['class'];
    return $class::run($command, $arguments);
  }
}