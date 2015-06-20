<?php

namespace Common\Menu;

/**
 * Menu themer.
 *
 * @package Common\Menu
 */
class Theme implements \HTML\Fragment\Themes {
  static function add() {
    return ['menu' => ['template' => 'packages/Common/Menu/twig/menu.twig']];
  }

  static function alter($theme) {
  }

  static function render($theme_name, $theme_data, $data) {
    switch ($theme_name) {
      case 'menu':
        //TODO: auto-generate "this component's directory" (components might not be in the main /packages dir)
        return filter('twig', $theme_data['template'], ['menu' => $data]);
    }
  }
}
