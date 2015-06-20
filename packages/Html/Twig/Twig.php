<?php

namespace HTML;

class Twig extends Filter {
  /**
   * @var \Twig_Environment
   */
  private $twig;

  protected static function formats() {
    return ['twig'];
  }

  function filter($format, $source_file, $parameters = NULL) {
    switch ($format) {
      case 'twig':
        return $this->twig()->render($source_file, !is_array($parameters) ? [] : $parameters);
    }
  }

  /**
   * Returns the Twig object. Initializes it if necessary.
   * @return \Twig_Environment
   */
  function twig() {
    if (is_null($this->twig)) {
      \Twig_Autoloader::register();
      $paths = ['.'];
      foreach (implementers('HTML\\Twig', 'Paths') as $implementer) {
        /** @var Twig\Paths $implementer */
        $to_add = $implementer::add();
        if ($to_add)
          foreach ($to_add as $path)
            $paths[] = $path;
      }
      $loader = new \Twig_Loader_Filesystem($paths);

      $dir = 'cache'
        .DIRECTORY_SEPARATOR.'twig';
      if (!file_exists($dir))
        mkdir($dir, 0777, TRUE);

      $this->twig = new \Twig_Environment($loader, ['cache' => $dir]);
    }

    return $this->twig;
  }

}
