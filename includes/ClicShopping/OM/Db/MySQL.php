<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Db;

use PDO;

/**
 * Represents a MySQL database connection and driver with specific implementations
 * and options for connecting to a MySQL database using PDO.
 *
 * Extends the ClicShopping\OM\Db class to provide MySQL-specific functionality and behavior.
 */

class MySQL extends \ClicShopping\OM\Db
{
  protected bool $connected;
  protected string $table_prefix;

  /**
   * Constructor method for initializing database connection parameters.
   *
   * @param string $server The database server hostname or IP address.
   * @param string $username The username for database authentication.
   * @param string $password The password for database authentication.
   * @param string $database The name of the database to connect to.
   * @param int $port The port number for the database connection.
   * @param array $driver_options An array of driver-specific options.
   * @param array $options Additional options for the database connection.
   *
   * @return void
   */
  public function __construct($server, $username, $password, $database, $port, $driver_options, $options)
  {
    $this->server = $server;
    /**
     *
     */
      $this->username = $username;
    $this->password = $password;
    $this->database = $database;
    $this->port = $port;
    $this->driver_options = $driver_options;
    /**
     *
     */
      $this->options = $options;

    if (!isset($this->driver_options[PDO::MYSQL_ATTR_INIT_COMMAND])) {
      // STRICT_ALL_TABLES 5.0.2
      // NO_ZERO_DATE 5.0.2
      // NO_ZERO_IN_DATE 5.0.2
      // ERROR_FOR_DIVISION_BY_ZERO 5.0.2
      // NO_ENGINE_SUBSTITUTION 5.0.8
      $this->driver_options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'set session sql_mode="STRICT_ALL_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION"';
    }

    return $this->connect();
  }

  /**
   * Establishes a connection to the database using the provided configuration settings
   * and returns the database handle.
   *
   * @return string|null Returns the database handle on a successful connection, or null if the connection failed.
   */
  public function connect(): string|null
  {
    $dsn_array = [];

    if (!empty($this->database)) {
      $dsn_array[] = 'dbname=' . $this->database;
    }

    if ((str_contains($this->server, '/')) || (str_contains($this->server, '\\'))) {
      $dsn_array[] = 'unix_socket=' . $this->server;
    } else {
      $dsn_array[] = 'host=' . $this->server;

      if (!empty($this->port)) {
        $dsn_array[] = 'port=' . $this->port;
      }
    }

    $dsn_array[] = 'charset=utf8mb4';

    $dsn = 'mysql:' . implode(';', $dsn_array);

    $this->connected = true;

    $dbh = parent::__construct($dsn, $this->username, $this->password, $this->driver_options);

    return $dbh;
  }
}
