<?php

namespace KPIs\Category;

class Schema implements \Database\Connection\Schema {
  //TODO: after the Schema interface exists, implement table "kpi_category"
  /**
   * @param \Doctrine\DBAL\Schema\Schema $schema
   * @param string $id
   * @return NULL
   */
  static function alter(&$schema, $id) {
    if (is_null($id)) {
      $table = $schema->createTable('kpi_category');
      $table->addColumn('id', 'integer');
      $table->addColumn('name', 'string');
      $table->setPrimaryKey(array('id'));
    }
  }

}
