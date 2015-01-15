<?php

namespace Cache;

use Core\Component;

class Cache extends Component {
  private static $reserved_bin_names = [
    'all',
    'external',
  ];

  private $bins;

  private $data = [];

  protected static function bins() {
    return ['default' => ['storage' => 'Cache\\Cache']];
  }

  /**
   * Set up the cache bins.
   */
  final function __construct() {
    //TODO: cache bins definitions
    $this->bins = self::bins();
    foreach (extenders(get_class(), TRUE, TRUE) as $extender) {
      /** @var self $extender */
      $this->bins = array_merge($this->bins, $extender::bins());
    }
//    return;
//    print_r($this->bins); exit;
//    foreach (implementers('Cache\\Cache', 'Bins', TRUE) as $implementer) {
//      /**
//       * @var Cache\Bins $implementer
//       */
//      foreach ($implementer::add() as $key => $data) {
//        if (in_array($key, self::$reserved_bin_names))
//          throw new \Exception('Cache bin name reserved: ' . $key);
//
//        if (isset($this->bins[$key]))
//          throw new \Exception('Cache bin already exists: ' . $key);
//
//        if (!isset($data['storage']))
//          $data['storage'] = $implementer;
//
//        $this->bins[$key] = $data;
//      }
//
//      foreach ($this->bins as $key => &$bin) {
//        $alters = $implementer::alter($key);
//        if ($alters)
//          foreach ($alters as $k => $v)
//            $bin[$k] = $v;
//      }
//    }
  }

  function set($key, $value, $bin = 'default') {
    if (!isset($this->bins[$bin]))
      //the specified bin is not available, use the default one.
      $bin = 'default';

    switch ($this->bins[$bin]['storage']) {
      case get_class():
        //store in this class directly
        $this->data[$key] = $value;
        break;
      default:
        /**
         * @var self $class
         */
        $class = $this->bins[$bin]['storage'];
        $class::i()->set($key, $value, $bin);
    }
  }

  function get($key, $bin = 'default') {
    if (!isset($this->bins[$bin]))
      //the specified bin is not available, use the default one.
      $bin = 'default';

    switch ($this->bins[$bin]['storage']) {
      case 'internal':
        if (isset($this->data[$key]))
          return $this->data[$key];
        break;
      default:
        /**
         * @var self $class
         */
        $class = $this->bins[$bin]['storage'];
        return $class::i()->get($key, $bin);
    }
  }

  /**
   * Clears all cached values, optionally filtered by bin.
   * @param string $bin
   * @return bool TRUE if successful
   */
  function clear($bin = NULL) {
    if (is_null($bin)) {
      //Clear all caches
      foreach ($this->bins as $bin => $data)
        $this->clear($bin);

      //Now clear external caches
      foreach (implementers('Cache\\Cache', 'External', TRUE) as $implementer) {
        /**
         * @var Cache\External $implementer
         */
        $implementer::clear();
      }

      return TRUE;
    }

    switch ($this->bins[$bin]['storage']) {
      case get_class():
        $this->data = [];
        return TRUE;
      default:
        /**
         * @var self $class
         */
        $class = $this->bins[$bin]['storage'];
        return $class::i()->clear($bin);
    }
  }

  /**
   * Returns an array of the available bin names.
   * @return array
   */
  function getBins() {
    return array_keys($this->bins);
  }
}
