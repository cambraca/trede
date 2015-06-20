<?php

namespace Entity\Entity;

trait SimpleDBMapper {
  abstract protected function table();
  function save() {
    echo 'TO DO: implement simpleDBmapper "save" method'.PHP_EOL;
  }
}

trait Fieldable {
  abstract protected function fields();
}

trait Page {
//????
}
