<?php

namespace HTML;

class StaticPages extends \Core\Component {
  protected static $initial_state = 'on';
  //TODO: load .md files from /static directory, add routes thru Routes api, handle requests by converting markdown and serving the pages.

  function loadRoutes($directory = '', $recursive = TRUE) {
    $root = 'static' . DIRECTORY_SEPARATOR;

    $ret = [];

    if (is_dir($root . $directory) && $dir_handle = opendir($root . $directory)) {
      while (FALSE !== ($entry = readdir($dir_handle))) {
        if ($recursive && is_dir($root . $directory . DIRECTORY_SEPARATOR . $entry) && !in_array($entry, ['.', '..'])) {
          $ret += $this->loadRoutes(trim($directory . DIRECTORY_SEPARATOR . $entry, DIRECTORY_SEPARATOR));
          continue;
        }

        if (!is_dir($entry)) {
          $pathinfo = pathinfo($entry);

          $filter = NULL;
          switch ($pathinfo['extension']) {
            case 'md':
              $filter = 'markdown';
              break;
            default:
              //Format unknown, skip this file.
              continue;
          }

          $ret[trim($directory . DIRECTORY_SEPARATOR . $pathinfo['filename'], DIRECTORY_SEPARATOR)] = [
            'type' => 'html',
            'filter' => $filter,
            'template' => $root . trim($directory . DIRECTORY_SEPARATOR . $pathinfo['basename'], DIRECTORY_SEPARATOR),
          ];
        }
      }
    }

    return $ret;

  }


}
