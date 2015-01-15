<?php

namespace HTTP;

use HTML\Page;

class Router extends \Core\Component {
  protected $routes = [];

  /**
   * Rebuilds route cache in database.
   */
  function rebuild() {
    foreach (implementers('HTML\\Router', 'ManageRoutes') as $routes) {
      /* @var Page\FilterOutput $routes */
    }
  }

  /**
   * Loads routing data for the given URL path.
   * @param string|NULL $url_path
   *  For example: "/blog/first-post".
   *  If omitted, gets the current path from $_SERVER.
   */
  function load($url_path = NULL) {
    //TODO: lookup in defined routes matching wildcards, etc.
    return [
      'type' => 'html',
      'template' => '',
      ''
    ];
  }
}
