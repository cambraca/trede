<?php

namespace Html;

class Metatags extends \Core\Component {
  static $title = 'HTML Metatags';
  static $description = 'Renders HTML meta tags.';
  static $dependencies = [
    '\Html\Page',
  ];

  public function render() {
    $html = '<!DOCTYPE html>'.PHP_EOL;
    $html .= '<html>';
    $html .= '<head>'.$this->head().'</head>';
    $html .= '<body>'.$this->body().'</head>';
    $html .= '</html>';
    return $html;
  }

  function head() {
    return '<title>test</title>';
  }

  function body() {
    return '<h1>this is a page</h1>';
  }

  function run() {
    print $this->render();
  }
}

//function html_renderer_run() {
//
//}

//tests
//html_renderer_head_append(new Renderer\Element('script', array('type' => 'text/javascript', 'src' => 'http://jquery.js')));