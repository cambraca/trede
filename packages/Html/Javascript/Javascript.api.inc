<?php

namespace HTML\Javascript;

use \Core\HookImplementer;

interface Includes extends HookImplementer {
  const TYPE_FILE = 'file';
  const TYPE_CODE = 'code';

  /**
   * Adds Javascript to the page.
   * @return array
   *  The format of the array is the following:
   *    [
   *      'jQuery' => ['weight' => -20, 'type' => TYPE_FILE, 'path' => 'packages/jQuery/jQuery/js/jquery.min.js'],
   *      'my_custom_code' => ['type' => TYPE_CODE, 'code' => 'document.write("Hello world!");'],
   *      ...
   *    ]
   *  If weight is not specified, a value of zero is assumed.
   * @todo add option to include in <head> or at the bottom of <body>?
   */
  static function add();

  /**
   * @todo add description
   */
  static function alter();

  /**
   * @todo add description
   */
  static function remove();
}
