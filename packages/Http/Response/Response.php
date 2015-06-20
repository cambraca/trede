<?php

namespace HTTP;

use Core\Component;
use HTML\Page;

//$component = [
//  'title' => 'HTTP Response',
//  'description' => 'Can return an HTTP response.'
//];

class Response extends Component {
  /**
   * Sends the appropriate HTTP headers for the current response.
   */
  function sendHeaders($route) {

//    header();
  }

  function serve() {
    list($path, $route) = Router::i()->load();
    $this->sendHeaders($route);

    switch ($route['type']) {
      case 'html':
        return Page::i()->render($path, $route);
      case 'xml':
        break;
      case 'json':
        break;
      default:
        throw new \Exception('Response type not defined: ' . $route['type']);
    }
  }
}
