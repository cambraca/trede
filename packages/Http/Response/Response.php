<?php

namespace Http;

use Core\Component;

$component = [
  'title' => 'HTTP Response',
  'description' => 'Can return an HTTP response.'
];

class Response extends Component {
  /**
   * Sends the appropriate HTTP headers for the current response.
   */
  function sendHeaders() {

//    header();
  }

  function serve() {
    $this->sendHeaders();
    print Router::i()->render();
  }
}