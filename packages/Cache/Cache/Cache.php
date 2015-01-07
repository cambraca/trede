<?php

namespace Cache;

use Core\Component;

class Cache extends Component {
  private $bins = [
    'default' => [
      'storage' => 'internal',
    ],
  ];

  private $data = [];

  /**
   * Set up the cache bins.
   */
  function __construct() {
    foreach (implementers('Cache\\Cache', 'Bins') as $implementer) {
      /**
       * @var Cache\Bins $implementer
       */
      foreach ($implementer::add() as $key => $data) {
        if (isset($this->bins[$key]))
          throw new \Exception('Cache bin already exists: ' . $key);

        if (!isset($data['storage']))
          $data['storage'] = $implementer;

        $this->bins[$key] = $data;
      }

      foreach ($this->bins as $key => &$bin) {
        $alters = $implementer::alter($key);
        if ($alters)
          foreach ($alters as $k => $v)
            $bin[$k] = $v;
      }
    }
  }

  function set($key, $value, $bin = 'default') {
    if (!isset($this->bins[$bin]))
      //the specified bin is not available, use the default one.
      $bin = 'default';

    switch ($this->bins[$bin]['storage']) {
      case 'internal':
        //store in this class directly
        $this->data[$key] = $value;
        break;
      default:
        /**
         * @var Cache\Bins $class
         */
        $class = $this->bins[$bin]['storage'];
        $class::set($key, $value, $bin);
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
         * @var Bins $class
         */
        $class = $this->bins[$bin]['storage'];
        return $class::get($key, $bin);
    }
  }

  /**
   * Clears all cached values, optionally filtered by bin.
   * @param string $bin
   */
  function clear($bin = NULL) {
    if (is_null($bin)) {
      foreach ($this->bins as $bin => $data)
        $this->clear($bin);
    }

    switch ($this->bins[$bin]['storage']) {
      case 'internal':
        $this->data = [];
        break;
      default:
        /**
         * @var Bins $class
         */
        $class = $this->bins[$bin]['storage'];
        return $class::clear($bin);
    }
  }
}