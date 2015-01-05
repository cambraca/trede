<?php

namespace Core;

use Http\Response;
use Http\Router;
use Cli\Cli;
use System\Alias;

class Bootstrap {
  static function bootstrap() {
    //composer autoloader
    include_once 'vendor/autoload.php';

    //our autoloader
    include_once 'Autoload.php';

    //Initialize aliases if they haven't been initialized yet.
    Alias::i();

    if (php_sapi_name() == 'cli') {
      if (class_exists('PHPUnit_Framework_TestCase')) {
        echo 'Running Bootstrap for PHPUnit'.PHP_EOL;
      } else {
        print Cli::i()->run();
        print PHP_EOL;
      }
    } else {
      //running from web server
      print Response::i()->serve();
    }
  }
}