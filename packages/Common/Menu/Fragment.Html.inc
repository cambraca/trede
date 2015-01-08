<?php

namespace Common\Menu;

use Html\Fragment\Themes;

/**
 * Menu themer.
 *
 * @package Common\Menu
 */
class Theme implements Themes {
  static function add() {
    return ['menu' => []];
  }

  static function alter($theme) {
  }

  static function render($theme, $data) {
    //TODO: remove this dummy data
    $data = [
      'home' => ['path' => '/', 'label' => 'Homepage'],
      'about' => ['path' => '/about', 'label' => 'About us'],
      'info' => ['label' => 'Information', 'children' => [
        'portfolio' => ['path' => '/portfolio', 'label' => 'Our awesome portfolio'],
        'clients' => ['path' => '/clients', 'label' => 'Learn about our clients'],
      ]],
    ];

    switch ($theme) {
      case 'menu':
        //TODO: auto-generate "this component's directory" (components might not be in the main /packages dir)
        return twig()->render('packages/Common/Menu/twig/menu.twig', ['menu' => $data]);
    }
  }
}
