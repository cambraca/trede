<?php

namespace Cli;

use Cache\Cache;
use Common\Menu\PresentMenu;
use Core\Component;

class Cli extends Component {
  /**
   * @var array List of commands. The "help" command is special, but all others
   * contain a "class" key that indicates the class that handles their execution.
   */
  protected $commands = NULL;

  function __construct() {
    $this->loadCommands();
  }

  private function loadCommands() {
    if ($this->commands)
      return;

    $this->commands = Cache::i()->get('cli_commands', 'file');
    if ($this->commands)
      return;

    $this->commands = [
      'help' => [],
    ];

    foreach (implementers('Cli\\Cli', 'Commands', TRUE) as $command) {
      /* @var Cli\Commands $command */
      $this->commands = array_merge($this->commands, $command::add());
    }

    Cache::i()->set('cli_commands', $this->commands, 'file');
  }

  function run() {
    global $argc, $argv;
    if ($argc < 2)
      return 'No command specified.';

    $arguments = $argv;
    array_shift($arguments); //generally, 'index.php'
    $command = array_shift($arguments);

    if (!isset($this->commands[$command]))
      return "Command unknown or deleted: $command";

    print "== TREDE == CLI mode ==\n";
    print "Running command: $command\n";

    if ($command == 'help' && !isset($this->commands['help']['class']))
      return $this->help($arguments ? array_shift($arguments) : NULL);

    /** @var Cli\Commands $class */
    $class = $this->commands[$command]['class'];
    return $class::run($command, $arguments);
  }

  function standalone($operation, $arguments) {
    //TODO: this is temporary code
    return PresentMenu::run($operation, $arguments);
  }

  /**
   * Provides help to the user, either about the system or for a specific
   * command.
   * @param string|NULL $command
   * @return NULL
   */
  private function help($command = NULL) {
    if ($command) {
      if (!isset($this->commands[$command]))
        return <<<EOS
Command not found: $command
Try "help" for information.
EOS;
      /** @var Cli\Commands $class */
      $class = $this->commands[$command]['class'];
      return $class::help($command);
    } else {
      $all_commands = array_keys($this->commands);
      unset($all_commands[array_search('help', $all_commands)]);

      sort($all_commands);
      $command_array = array_merge(['Available commands:'], $all_commands);
      $command_list = '';
      $chars = 0;
      foreach ($command_array as $command) {
        if ($chars && ($chars + strlen($command) > 60)) {
          $chars = 0;
          $command_list .= PHP_EOL;
        }
        $chars += strlen($command);
        $command_list .= ($command_list ? ' ' : '') . $command;
      }
      return <<<EOS
Run any command defined for the Cli component.
To get help for a specific command, try "help [command]".

$command_list

EOS;
    }
  }
}
