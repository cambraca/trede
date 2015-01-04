<?php

namespace Html\Page;

class Element {
  /**
   * @var string
   */
  protected $tag_name = '';

  /**
   * @var array
   */
  public $attributes = [];

  /**
   * @var null|string
   */
  public $value = NULL;

  public function __construct($tag_name, $attributes = [], $value = NULL) {
    $this->tag_name = $tag_name;

    if (is_array($attributes)) {
      $this->attributes = $attributes;
    }

    $this->value = $value;
  }

  public function tagName($tag_name) {
    $this->tag_name = trim($tag_name);
  }

  public function __toString() {
    return '<'.$this->tag_name.' />';
  }
}