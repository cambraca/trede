<?php

namespace Http;

use Core\Component;
use Html\Page;

class Router extends Component {
  protected $routes = [];

  protected function loadRoutes() {
    foreach (implementers('Html\\Router', 'ManageRoutes') as $routes) {
      /* @var Page\FilterOutput $routes */
    }
  }

  function render() {
    $this->loadRoutes();

    //TODO: do the actual "routing" logic

    $type = 'json';
    $type = 'xml';
    $type = 'html';

    if ($type == 'html') {
      return Page::i()->render();
    }
  }
}