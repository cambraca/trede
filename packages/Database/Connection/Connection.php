<?php

namespace Database;

use System\Settings;

class Connection extends \Core\Component {
  /**
   * @var string|NULL
   * Identifier for the connection.
   */
  private $id;

  /**
   * @var \Doctrine\DBAL\Connection
   */
  private $conn;

  function __construct($id = NULL) {
    $this->id = $id;
  }

  /**
   * Returns a query builder.
   * @return \Doctrine\DBAL\Query\QueryBuilder
   */
  function query() {
    if (!$this->conn)
      $this->setup();

    return $this->conn->createQueryBuilder();
  }

  /**
   * Gets the database connection for the current instance.
   * Multiple database connections can be set by creating multiple objects from
   * this component's class. There is usually a primary one, though, whose
   * options are defined by System\Settings.
   * @return \Doctrine\DBAL\Connection
   */
  function conn() {
    if (!$this->conn)
      $this->setup();

    return $this->conn;
  }

  /**
   * @todo Use the Settings component to store/retrieve the connection params for the connection ID to load.
   */
  private function setup() {
    $settings = Settings::i();

    $this->conn = \Doctrine\DBAL\DriverManager::getConnection([
      'dbname' => $settings->get(get_class(), 'dbname'),
      'host' => $settings->get(get_class(), 'host'),
      'user' => $settings->get(get_class(), 'user'),
      'password' => $settings->get(get_class(), 'password'),
      'driver' => 'pdo_mysql',
    ]);
  }
}
