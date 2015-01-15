<?php

/**
 * @todo
 * create a CLI interface (show the menu, let the user select an option,
 * recursive for submenus, finally execute some command).
 * An example is the "clear cache" CLI command, which should present the user
 * with the cache bins as options (and a "clear all caches" option too).
 */


namespace Common\Menu;

use Cli\Cli\Standalone;

class PresentMenu implements Standalone {
  static function run($operation, $menu, $parent = FALSE) {
    if ($operation != 'menu')
      return;

    do {
      $i = 'a';
      $available = [];
      print PHP_EOL;
      foreach ($menu as $menu_item) {
        $available[$i] = $menu_item;
        print "  $i. {$menu_item['label']}\n";
        $i++;
      }
      $parent_option = NULL;
      if ($parent) {
        $parent_option = $i;
        $available[$i] = '';
        print "  $i. [go back]\n";
      }
      print "Select an option: ";

      $selection = NULL;
      do {
        if (!is_null($selection)) {
          print "Invalid selection, please select a valid option: ";
        }
        $selection = trim(strtolower(fgets(STDIN)));
      } while (!array_key_exists($selection, $available));

      if (!is_null($parent_option) && $selection == $parent_option) {
        //"Go back to parent menu"
        return FALSE;
      }
      elseif (isset($available[$selection]['callback'])) {
        //Normal menu item
        $callable = array_shift($available[$selection]['callback']);
        $params = $available[$selection]['callback'] ? array_shift($available[$selection]['callback']) : [];
        return call_user_func_array($callable, $params);
      }

      $ret = TRUE;

      if (isset($available[$selection]['children']))
        $ret = self::run($operation, $available[$selection]['children'], TRUE);

      if ($ret)
        return TRUE; //All good
    } while ($ret === FALSE);
  }
}
