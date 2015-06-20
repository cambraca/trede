<?php

namespace HTML;
use Common\Menu;
use \Core\Component;

class Page extends Component {
  static $title = 'HTML Page';
  static $description = 'Knows how to render a web page.';

  function render($path, $route) {
    $html = '<!DOCTYPE html>'.PHP_EOL;
    $html .= '<html>';
    $html .= '<head>'.$this->head($path, $route).'</head>';
    $html .= '<body>'.$this->body($path, $route).'</head>';
    $html .= '</html>';

    Minimize::activate();

    foreach (implementers('HTML\\Page', 'FilterOutput') as $filter) {
      /* @var Page\FilterOutput $filter */
      $html = $filter::filter($html);
    }

    return $html;
  }

  function head() {
    $html = '<title>test</title>';

    //TODO: this is temporary
    $html .= '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>';

    foreach (implementers('HTML\\Page', 'Head') as $filter) {
//      echo $filter.'. ';
      /* @var Page\Head $filter */
      $items = $filter::append();
      if ($items)
        foreach ($items as $item)
          $html .= $item; //TODO: track item IDs so other hooks can modify/replace/remove these blocks of html. Also, implement weights for these items (or for the implementors?)
    }

    return $html;
  }

  function body($path, $route) {
//    print_r($route);
    if (isset($route['callback']) && $route['callback']) {
      /** @var \HTTP\Router\Routes $class */
      $class = $route['class'];
      $method = $route['callback'];
      $arguments = isset($route['arguments']) ? $route['arguments'] : NULL;
      return $class::$method($route, $arguments);
    }

    if (isset($route['filter']))
      return filter($route['filter'], $route['template']); //TODO: do this right
//    return twig()->render(location('HTML\\Template', 'twig/default.twig'), ['title' => 'hi']);

    $html = '<h1> <span>this is a page</span>   </h1>';
//    $html .= '<pre>'.print_r(
//      conn()->fetchAll(
//        query()
//          ->select('nid', 'title')
//          ->from('node', 'n')
//          ->setMaxResults(5)
//      ), TRUE
//    ).'</pre>';

    $html .= Fragment::i()->render('menu', [
      'home' => ['path' => '/', 'label' => 'Homepage'],
      'about' => ['path' => '/about', 'label' => 'About us'],
      'info' => ['label' => 'Information', 'children' => [
        'portfolio' => ['path' => '/portfolio', 'label' => 'Our awesome portfolio'],
        'clients' => ['path' => '/clients', 'label' => 'Learn about our clients'],
      ]],
      'logout' => ['path' => '/logout', 'label' => 'Log out'],
    ]);
    return $html;
  }

}
