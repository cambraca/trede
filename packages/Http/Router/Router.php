<?php

namespace HTTP;

class Router extends \Core\Component {
  protected $routes = [];

  function __construct() {
    $this->rebuild(); //TODO: this is temporary
  }

  /**
   * Rebuilds route cache in database.
   */
  function rebuild() {
    foreach (implementers('HTTP\\Router', 'Routes') as $implementer) {
      /* @var Router\Routes $implementer */
      $to_add = $implementer::add();
      foreach ($to_add as $path => $route) {
        if (isset($this->routes[$path]))
          throw new \Exception('Route already defined for path: ' . $path);

        if (!isset($route['class']))
          $route['class'] = $implementer;

        $this->routes[$path] = $route;
      }
    }
  }

  /**
   * Loads routing data for the given URL path.
   * @param string|NULL $path
   *  For example: "blog/first-post".
   *  If omitted, gets the current path from $_SERVER.
   * @return array
   */
  function load($path = NULL) {
    if (is_null($path)) {
      //Look in $_SERVER
      $path = trim($_SERVER['REQUEST_URI'], '/');
    }

    if (array_key_exists($path, $this->routes))
      return [$path, $this->routes[$path]];

    //TODO: lookup in defined routes matching wildcards, etc.
    return ['/', [
      'type' => 'html',
      'template' => '',
      ''
    ]];
  }
}
