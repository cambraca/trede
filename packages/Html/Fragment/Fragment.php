<?php

namespace HTML;

use Cache\Cache;
use Core\Component;

/**
 * Fragment component.
 * Used for creating pieces of HTML that may be assembled to create a Page.
 * A fragment can be created from an HTML string directly, or from a structured
 * object that can be rendered as HTML later.
 * Examples of fragments:
 *  - A menu containing structured data about the menu items, which might be
 *    rendered like this:
 *    <nav>
 *      <ul>
 *        <li><a href="item1">Item 1</a></li>
 *        <li><a href="item2">Item 2</a></li>
 *      </ul>
 *    </nav>
 *  - A blog post, which could be loaded from a database, and rendered like
 *    this:
 *    <article>
 *      <header><h1>My blog post</h1></header>
 *      <aside>Posted by John Doe on Jan 1st.</aside>
 *      <p>First paragraph.</p>
 *      <p>Second paragraph.</p>
 *    </article>
 * @package HTML
 */
class Fragment extends Component {
  /**
   * @var \Twig_Environment
   */
  private $twig;
  private $themes = [];

  /**
   * Set up the themes.
   */
  function __construct() {
    $this->loadThemes();
  }

  private function loadThemes() {
    if ($this->themes)
      return;

    $this->themes = Cache::i()->get('themes', 'file');
    if ($this->themes)
      return;

    foreach (implementers('HTML\\Fragment', 'Themes', TRUE) as $implementer) {
      /**
       * @var Fragment\Themes $implementer
       */
      foreach ($implementer::add() as $key => $data) {
        if (isset($this->themes[$key]))
          throw new \Exception('Theme already exists: ' . $key);

        if (!isset($data['renderer']))
          $data['renderer'] = $implementer;

        $this->themes[$key] = $data;
      }

      foreach ($this->themes as $key => &$theme) {
        $alters = $implementer::alter($key);
        if ($alters)
          foreach ($alters as $k => $v)
            $theme[$k] = $v;
      }
    }

    Cache::i()->set('themes', $this->themes, 'file');
  }

  /**
   * Returns the Twig object. Initializes it if necessary.
   * @return \Twig_Environment
   */
  function twig() {
    if (is_null($this->twig)) {
      \Twig_Autoloader::register();
      $loader = new \Twig_Loader_Filesystem('.');

      $dir = 'cache'
        .DIRECTORY_SEPARATOR.'twig';
      if (!file_exists($dir))
        mkdir($dir, 0777, TRUE);

      $this->twig = new \Twig_Environment($loader, ['cache' => $dir]);
    }

    return $this->twig;
  }

  /**
   * Render the given data using the specified theme.
   * @param string $theme_name
   * @param mixed $data
   * @return string
   * @throws \Exception
   */
  function render($theme_name, $data) {
    if (!isset($this->themes[$theme_name]))
      throw new \Exception('Theme not available: '.$theme_name);

    /**
     * @var Fragment\Themes $class
     */
    $class = $this->themes[$theme_name]['renderer'];
    return $class::render($theme_name, $this->themes[$theme_name], $data);
  }
}
