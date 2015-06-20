<?php

namespace HTML;

use \Michelf\MarkdownExtra;

class Markdown extends Filter {
  protected static function formats() {
    return ['markdown' => ['parameters' => FALSE]];
  }

  function filter($format, $source_file, $parameters = NULL) {
    switch ($format) {
      case 'markdown':
        return MarkdownExtra::defaultTransform(file_get_contents($source_file));
    }
  }
}
