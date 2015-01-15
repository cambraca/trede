<?php

namespace HTML\Fragment;

use Core\HookImplementer;

interface Themes extends HookImplementer {
  /**
   * Adds new themes. Each array entry is assigned a "renderer" key with the
   * implementer's class name, unless explicitly specified.
   * @return array|NULL
   */
  static function add();

  /**
   * Alters properties of any theme. For example, altering the renderer for
   * the "menu" theme to render the menu using different HTML.
   * @param string $theme
   * @return array|NULL
   */
  static function alter($theme);

  /**
   * Renders the given data using the specified theme.
   * @param string $theme_name
   * @param array $theme_data
   *  The theme options, as defined by add().
   * @param mixed $data
   * @return string A valid HTML fragment.
   */
  static function render($theme_name, $theme_data, $data);
}
