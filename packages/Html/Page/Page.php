<?php

namespace HTML;
use Common\Menu;
use \Core\Component;

class Page extends Component {
  static $title = 'HTML Page';
  static $description = 'Knows how to render a web page.';

  function render() {
    $html = '<!DOCTYPE html>'.PHP_EOL;
    $html .= '<html>';
    $html .= '<head>'.$this->head().'</head>';
    $html .= '<body>'.$this->body().'</head>';
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

    foreach (implementers('HTML\\Page', 'Head') as $filter) {
      /* @var Page\Head $filter */
      foreach ($filter::append() as $item) {
        if ($item)
          $html .= $item; //TODO: track item IDs so other hooks can modify/replace/remove these blocks of html. Also, implement weights for these items (or for the implementors?)
      }
    }

    return $html;
  }

  function body() {
//    return twig()->render(location('HTML\\Template', 'twig/default.twig'), ['title' => 'hi']);
    $html = '<h1> <span>this is a page</span>   </h1>';
    $html .= '<pre>'.print_r(
      conn()->fetchAll(
        query()
          ->select('nid', 'title')
          ->from('node', 'n')
          ->setMaxResults(5)
      ), TRUE
    ).'</pre>';

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
