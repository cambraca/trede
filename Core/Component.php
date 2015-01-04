<?php

namespace Core;

abstract class Component {
  /**
   * @var string
   */
  static $version = '1.0';

  /**
   * @var string
   */
  static $title;

  /**
   * @var string
   */
  static $description;

  /**
   * @var array
   */
  static $dependencies = [];

  /**
   * @var bool
   */
  private $enabled = TRUE;

  /**
   * @var array
   */
  private static $instances = [];

  /**
   * @var array
   * Describes all components installed in the system. This is cached in a file.
   */
  private static $components = [];

  /**
   * @return mixed
   */
  function run() {}

  function enable() {
    $this->enabled = TRUE;
  }

  function disable() {
    $this->enabled = FALSE;
  }

  /**
   * Get component instance.
   * @return static
   */
  static function i() {
    $class = get_called_class();
    if (!isset(self::$instances[$class])) {
      self::$instances[$class] = new $class();
    }

    return self::$instances[$class];
  }
}