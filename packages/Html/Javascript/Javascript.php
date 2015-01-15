<?php

namespace HTML;

class Javascript extends \Core\Component {
  private $includes = [];

  function __construct() {
    foreach (implementers('HTML\\Javascript', 'Includes') as $implementer) {
      /** @var Javascript\Includes $implementer */
      foreach ($implementer::add() as $id => $to_add) {
        //TODO: add validations and sort by weight
        $this->includes[$id] = $to_add;
      }
    }
  }

  function getIncludes() {
    return $this->includes;
  }
}