<?php

namespace Core;

use HTTP\Response;
use HTTP\Router;
use Cli\Cli;
use System\Alias;

class Bootstrap {
  private static $settings = [
    'development_mode' => FALSE,
  ];

  static function isDevelopmentMode() {
    return (bool) self::$settings['development_mode'];
  }

  static function bootstrap() {
    //Composer autoloader
    include_once 'vendor/autoload.php';

    //Our autoloader
    include_once 'Autoload.php';

    //Read bootstrap settings
    $settings_filename = 'settings/Core_Bootstrap.json';
    if (file_exists($settings_filename)) {
      self::$settings = json_decode(file_get_contents($settings_filename), TRUE) + self::$settings;
    }

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

    Component::finalizeAll();
  }
}
