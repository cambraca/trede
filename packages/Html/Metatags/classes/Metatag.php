<?php

namespace HTML\Metatags;
use HTML\Page\Element;

class Metatag extends Element {
  protected $tag_name = 'meta';
  public $attributes = ['name' => '', 'content' => ''];

  function __toString() {
    if ($this->attributes['name'] && $this->attributes['content'])
      return parent::__toString();
    else
      return '';
  }
}
