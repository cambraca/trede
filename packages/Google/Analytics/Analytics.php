<?php

namespace Google;

use System\Settings;
use Core\Component;

class Analytics extends Component {
  private $tracking_id;

  /**
   * @return string|null
   */
  public function trackingId() {
    Settings::i()->set('Google\\Analytics', 'tracking_id', 'ua-algoalgo'); //tmp


    if (!$this->tracking_id)
      $this->tracking_id = Settings::i()->get('Google\\Analytics', 'tracking_id');

    return $this->tracking_id;
    return 'UA-56809907-1';
  }
}
