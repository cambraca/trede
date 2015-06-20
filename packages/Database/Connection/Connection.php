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

  /**
   * @var \Doctrine\DBAL\Schema\AbstractSchemaManager
   */
  private $sm;

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

  function updateSchema() {
    if (!$this->sm)
      $this->setupSM();

    $from = $this->sm->createSchema();
    $to = $this->schema();

    $sql_commands = $from->getMigrateToSql($to, $this->conn->getDatabasePlatform());

    foreach ($sql_commands as $sql_command)
      $this->conn->query($sql_command);
  }

  /**
   * @return \Doctrine\DBAL\Schema\Schema
   */
  function schema() {
    $schema = new \Doctrine\DBAL\Schema\Schema();

    foreach (implementers('Database\\Connection', 'Schema', TRUE) as $implementer) { //TODO: all implementers? maybe only "on" and "auto"?
      /** @var Connection\Schema $implementer */
      $implementer::alter($schema, $this->id);
    }

    return $schema;
  }

  private function setupSM() {
    if (!$this->conn)
      $this->setup();

    $this->sm = $this->conn->getSchemaManager();
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
