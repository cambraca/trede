<?php

namespace Html;
use \Core\Component;

class Page extends Component {
  static $title = 'HTML Page';
  static $description = 'Knows how to render a web page.';

  function render() {
    $html = '<!DOCTYPE html>'.PHP_EOL;
    $html .= '<html>';
    $html .= '<head>'.self::head().'</head>';
    $html .= '<body>'.self::body().'</head>';
    $html .= '</html>';

    foreach (implementers('Html\\Page', 'FilterOutput') as $filter) {
      /* @var Page\FilterOutput $filter */
      $html = $filter::filter($html);
    }

    return $html;
  }

  function head() {
    $html = '<title>test</title>';

    foreach (implementers('Html\\Page', 'Head') as $filter) {
      /* @var Page\Head $filter */
      foreach ($filter::append() as $item) {
        if ($item)
          $html .= $item; //TODO: track item IDs so other hooks can modify/replace/remove these blocks of html. Also, implement weights for these items (or for the implementors?)
      }
    }

    return $html;
  }

  function body() {
    $html = '<h1> <span>this is a page</span>   </h1>';
    $html .= '<pre>'.print_r(
      conn()->fetchAll(
        query()
          ->select('nid', 'title')
          ->from('node', 'n')
          ->setMaxResults(5)
      ), TRUE
    ).'</pre>';
    $html .= Fragment::i()->render('menu', 'dummy');
    return $html;
  }

}
