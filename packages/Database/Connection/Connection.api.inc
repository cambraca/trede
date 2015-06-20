<?php

namespace Database\Connection;

use Core\HookImplementer;

interface Schema extends HookImplementer {
  /**
   * @param \Doctrine\DBAL\Schema\Schema $schema
   * @param string $id
   * @return NULL
   */
  static function alter(&$schema, $id);
}
