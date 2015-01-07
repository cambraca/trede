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
        //TODO: replace with something like twig or smarty (this functionality will probably come from the Fragment component)
        $html = '<nav><ul>';
        $render = function($data) use (&$render) {
          $html = '';
          foreach ($data as $item) {
            $html .= '<li>';
            if (isset($item['label'])) {
              //TODO: replace with the same twig/smarty thing, to escape characters, etc. Also, a nice "link" renderer would be nice.
              if (isset($item['path']))
                $html .= '<a href="'.$item['path'].'">'.$item['label'].'</a>';
              else
                $html .= '<span>'.$item['label'].'</span>';
            }
            if (isset($item['children']))
              $html .= '<ul>'.$render($item['children']).'</ul>';
            $html .= '</li>';
          }
          return $html;
        };
        $html .= $render($data);
        $html .= '</ul></nav>';
        return $html;
    }
  }

}