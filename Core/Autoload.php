<?php

namespace Core;

class Autoload {
  static function autoload($class) {

    //debug: these are called by phpunit..
    if (in_array($class, [
      'LazyMap\\AbstractLazyMap',
      'Instantiator\\Instantiator',
      'Composer\\Autoload\\ClassLoader',
      'PHPUnit_Extensions_Story_TestCase',
      'SebastianBergmann\\Exporter\\Exception',
    ]))
      return;

    $parts = explode('\\', $class);
    switch (count($parts)) {
      case 2:
        list($package, $component) = $parts;

        if ($package == 'Core')
          $main_file = 'Core'
            .DIRECTORY_SEPARATOR.$component.'.php';
        else
          $main_file = 'packages'
            .DIRECTORY_SEPARATOR.$package
            .DIRECTORY_SEPARATOR.$component
            .DIRECTORY_SEPARATOR.$component.'.php';

        if (file_exists($main_file)) {
          //component found
          include $main_file;
        } else {
          //is this component a dependency of the caller?
          if (FALSE) //TODO: change logic to: is dependency?
            echo 'to implement'.PHP_EOL;
          else {
            include Dummy::generateSubclass($package, $component);
          }
        }

        break;
      case 3:
        list($package, $component, $implementer) = $parts;
        $components = Component::definitions();
        if (isset($components["$package\\$component"]['api'][$implementer]))
          include_once $components["$package\\$component"]['location']
            . DIRECTORY_SEPARATOR . $component . '.api.inc';
        elseif (isset($components["$package\\$component"]['implementers'][$implementer]))
          include_once $components["$package\\$component"]['implementers'][$implementer]['file'];

        else {print_r($parts); debug_print_backtrace(0, 4); echo 'die'; exit;}
    }
  }
}

spl_autoload_register(['Core\Autoload', 'autoload']);
