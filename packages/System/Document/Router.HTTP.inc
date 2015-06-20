<?php

namespace System\Document;

class Routes implements \HTTP\Router\Routes {
  static function add() {
    $prefix = \System\Settings::i()->get('System\\Document', 'url_prefix');
    if (is_null($prefix))
      return [];

    $ret = [];
    foreach (implementers('System\\Document', 'Outline', TRUE) as $implementer) {
      /** @var Outline $implementer */
      self::routesFromOutline($implementer, $implementer::outline(), $ret);
    }

    return $ret;
  }

  static function remove() {
    // TODO: Implement remove() method.
  }

  static function alter() {
    // TODO: Implement alter() method.
  }

  /**
   * Recursive function, builds an array of routes suitable for the HTTP\Routes
   * component.
   * @param string $prefix
   * @param string $package
   * @param string $component
   * @param string $implementer
   * @param array $outline
   * @param array $routes
   * @return array
   */
  static private function routesFromOutline($implementer, $outline, &$routes, $counters = []) {
    list($package, $component) = explode('\\', $implementer);

    if (!is_array($routes))
      $routes = [];

    $prefix = \System\Settings::i()->get('System\\Document', 'url_prefix');
    $full_prefix = $prefix
      . ($prefix ? '/' : '')
      . strtolower($package)
      . '/'
      . strtolower($component)
      . '/';

    $counters[] = 1;
    foreach ($outline as $key => $value) {
      $children = NULL;
      $item = is_array($value) ? $key : $value;

      $counter_string = implode('_', $counters);
      $file_path = 'docs' . DIRECTORY_SEPARATOR . $counter_string . ' ' . $item . '.md';

      $routes[$full_prefix . strtolower(str_replace(' ', '-', $item))] = [ //TODO: transliteration, etc etc (using some HTTP component probably)
        'type' => 'html',
        'callback' => 'page',
        'arguments' => ['implementer' => $implementer],
        'filter' => 'markdown',
        'template' => location("$package\\$component", $file_path),
      ];

      if (is_array($value))
        self::routesFromOutline($implementer, $value, $routes, $counters);

      $counters[count($counters) - 1]++;
    }
  }

  private static function menuFromOutline($implementer, $outline) {
    list($package, $component) = explode('\\', $implementer);

    $menu = [];

    $prefix = \System\Settings::i()->get('System\\Document', 'url_prefix');
    $full_prefix = $prefix
      . ($prefix ? '/' : '')
      . strtolower($package)
      . '/'
      . strtolower($component)
      . '/';

    foreach ($outline as $key => $value) {
      $children = NULL;
      $item = is_array($value) ? $key : $value;

      $menu_key = $full_prefix . strtolower(str_replace(' ', '-', $item));

      $menu[$menu_key] = [ //TODO: transliteration, etc etc (using some HTTP component probably)
        'path' => $full_prefix . strtolower(str_replace(' ', '-', $item)),
        'label' => $item,
      ];

      if (is_array($value))
        $menu[$menu_key]['children'] = self::menuFromOutline($implementer, $value);
    }

    return $menu;
  }

  static function page($route, $args) {
    $implementer = $args['implementer'];
    list($package, $component) = explode('\\', $implementer);
    $menu = \HTML\Fragment::i()->render('menu', self::menuFromOutline($implementer, $implementer::outline()));
    $content = filter($route['filter'], $route['template']);
    return <<<EOS
<table border=2 width=100%>
  <tr valign=top>
    <td width=20%>
      Package: <strong>$package</strong>
      <br />
      Component: <strong>$component</strong>
      $menu
    </td>
    <td>$content</td>
  </tr>
</table>
EOS;
  }

}
