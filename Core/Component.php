<?php

namespace Core;

use Cache\Cache;
use System\Alias;

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
   * Describes all components installed in the system. This is cached using the
   * Cache\File component.
   */
  private static $definitions;

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

  /**
   * Gets rid of all component instances.
   */
  static function resetAll() {
    self::$instances = [];
  }

  static function definitions() {
    if (is_null(self::$definitions)) {
      //Load from cache. We can't use the Cache component here since we can't
      //initialize any component at this point, so we fake the read operation
      //from the Cache\File component.
      $filename = 'cache'
        .DIRECTORY_SEPARATOR.'file'
        .DIRECTORY_SEPARATOR.'components.json';
      if (file_exists($filename)) {
        $cache = json_decode(file_get_contents($filename), TRUE);
        if (is_array($cache) && isset($cache['components']))
          self::$definitions = $cache['components'];
      }
//      self::$definitions = Cache::i()->get('components', 'file');
    }

    if (is_null(self::$definitions))
      self::rebuildDefinitions();

    return self::$definitions;
  }

  /**
   * Rebuild components array.
   * @param bool $cache_results
   *  If TRUE, uses the Cache\File component to store the results.
   * @param array $dummy_array
   *  If specified, skips automatic discovery and uses the given array instead
   *  (used mainly for testing purposes). Never caches results in this case.
   */
  static function rebuildDefinitions($cache_results = TRUE, $dummy_array = NULL) {
    if (is_array($dummy_array)) {
      self::$definitions = $dummy_array;
      Alias::i(); //see note below
      return;
    }

    //automatic discovery (reads all files in the packages folder)
    $components = [
      'System\\Alias' => [
        'api' => [
          'Aliases' => [
            'Database\\Connection\\Aliases',
          ],
        ],
      ],
      'Cache\\Cache' => [
        'api' => [
          'Bins' => [
            'Cache\\File\\FileBin',
          ],
        ],
      ],
      'Cache\\File' => [],
      'Database\\Connection' => [],
      'Html\\Page' => [
        'api' => [
          'FilterOutput' => [
//        'Html\\Minimize\\FilterOutput',
          ],
          'Head' => [
            'Google\\Analytics\\RenderTrackingCode',
          ],
        ],
      ],
      'System\\Settings' => [
        'api' => [
          'StorageType' => [],
          'Variables' => [
            'Google\\Analytics\\Options',
            'Database\\Connection\\DBSettings',
          ]
        ],
      ],
      'Html\\Minimize' => [],
      'Html\\Metatags' => [],
      'Google\\Analytics' => [
//    'implements' => [
//      'Core\\'
//    ]
      ],
      'Cli\\Cli' => [
        'api' => [
          'Commands' => [
            'Cron\\Cron\\CliRunner',
            'Cache\\Cache\\ClearCache',
          ],
        ],
      ],
      'Cron\\Cron' => [],
    ];

    self::$definitions = $components;

    //We need to initialize the Alias component here since it is necessary for
    //many other components, including Cache (used below).
    Alias::i();

    Cache::i()->set('components', $components, 'components');
  }
}
