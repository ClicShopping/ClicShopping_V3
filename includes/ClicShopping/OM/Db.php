<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM;

use ArrayIterator;
use CachingIterator;
use Exception;
use PDO;
use PDOStatement;
use function call_user_func_array;
use function count;
use function func_get_args;
use function is_array;
use function is_null;
use function is_string;
use function strlen;

/**
 * ClicShopping\OM\Db is a class extending PDO to manage database interactions
 * with additional features for query preparation, execution, and table prefixing.
 */
class Db extends PDO
{
  protected bool $connected = false;
  protected string $server;
  protected string $username;
  protected string $password;
  protected string $database;
  protected string $table_prefix;
  protected int|null $port;
  protected ?array $driver_options = [];
  protected ?array $options = [];
  protected $query_call;

  /**
   * Initializes a database connection.
   *
   * @param string|null $server The database server address. Defaults to the application configuration if not provided.
   * @param string|null $username The database username. Defaults to the application configuration if not provided.
   * @param string|null $password The database user password. Defaults to
   */
  public static function initialize(
    $server = null,
    $username = null,
    $password = null,
    $database = null,
    $port = null,
    array $driver_options = null,
    array $options = null
  )
  {
    if (!isset($server)) {
      $server = CLICSHOPPING::getConfig('db_server');
    }

    if (!isset($username) && CLICSHOPPING::configExists('db_server_username')) {
      $username = CLICSHOPPING::getConfig('db_server_username');
    }

    if (!isset($password) && CLICSHOPPING::configExists('db_server_password')) {
      $password = CLICSHOPPING::getConfig('db_server_password');
    }

    if (!isset($database) && CLICSHOPPING::configExists('db_database')) {
      $database = CLICSHOPPING::getConfig('db_database');
    }

    if (!is_array($driver_options)) {
      $driver_options = [];
    }

    if (!isset($driver_options[PDO::ATTR_PERSISTENT]) && CLICSHOPPING::configExists('db_server_persistent_connections')) {
      if (CLICSHOPPING::getConfig('db_server_persistent_connections') === 'true') {
        $driver_options[PDO::ATTR_PERSISTENT] = true;
      }
    }

    if (!isset($driver_options[PDO::ATTR_ERRMODE])) {
      $driver_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    }

    if (!isset($driver_options[PDO::ATTR_DEFAULT_FETCH_MODE])) {
      $driver_options[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_ASSOC;
    }

    if (!isset($driver_options[PDO::ATTR_STATEMENT_CLASS])) {
      $driver_options[PDO::ATTR_STATEMENT_CLASS] = ['ClicShopping\OM\DbStatement'];
    }

    if (!is_array($options)) {
      $options = [];
    }

    $object = false;

    try {
      $class = 'ClicShopping\OM\Db\MySQL';
      $object = new $class($server, $username, $password, $database, $port, $driver_options, $options);
    } catch (Exception $e) {
      $message = $e->getMessage();
        // Uncomment this line if you want to log the stack trace
//      $message .= "\n" . $e->getTraceAsString(); // the trace will contain the password in plain text

      if (!isset($options['log_errors']) || ($options['log_errors'] === true)) {
        error_log('ClicShopping\OM\Db::initialize(): ' . $message);
      }

      throw new Exception($message, $e->getCode());
    }

    return $object;
  }

  /**
   * Executes an SQL statement and returns the number of affected rows or false on failure.
   *
   * @param string $statement The SQL statement to execute. The statement should not return a result set.
   *
   * @return int|false Returns the number of rows that were affected by the executed statement,
   *                   or false if the execution fails.
   */
  public function exec(string $statement): int|false
  {
    $statement = $this->autoPrefixTables($statement);

    return parent::exec($statement);
  }

  /**
   * Prepares an SQL statement for execution, with optional driver options.
   *
   * @param string $statement The SQL statement to prepare, with placeholders for bound parameters if applicable.
   * @param array|null $driver_options An optional array of driver-specific options to set for the statement.
   * @return PDOStatement|false Returns a PDOStatement object if the preparation is successful, or false on failure.
   */
  public function prepare(string $statement, ?array $driver_options = null): PDOStatement|false
  {
    $statement = $this->autoPrefixTables($statement);

    $DbStatement = parent::prepare($statement, is_array($driver_options) ? $driver_options : []);
    $DbStatement->setQueryCall('prepare');
    $DbStatement->setPDO($this);

    return $DbStatement;
  }

  /**
   * Executes a SQL query with optional parameters and returns the prepared statement.
   *
   * @param string $statement The SQL query to execute.
   * @param mixed ...$params Optional parameters to bind to the query.
   * @return PDOStatement|false Returns the PDOStatement object if the query was successful, or false on failure.
   */
  public function query(string $statement, ...$params): PDOStatement|false
  {
    $statement = $this->autoPrefixTables($statement);

    $args = func_get_args();

    if (count($args) > 1) {
      $DbStatement = call_user_func_array(array($this, 'parent::query'), $args);
    } else {
      $DbStatement = parent::query($statement);
    }

    if ($DbStatement !== false) {
      $DbStatement->setQueryCall('query');
      $DbStatement->setPDO($this);
    }

    return $DbStatement;
  }

  /**
   * Retrieves data from the specified table with optional conditions, ordering, limits, and caching.
   *
   * @param string|array $table The name of the table or an array of table names. If not prefixed, a default prefix is applied.
   * @param string|array $fields The fields to select from the table.
   * @param array|null $where An associative array of conditions for the query. Can include operators and compound relationships.
   * @param string|array|null $order The
   */
  public function get($table, $fields, array|null $where = null, $order = null, $limit = null, $cache = null, array|null $options = null)
  {
    if (!is_array($table)) {
      $table = [
        $table
      ];
    }

    if (!isset($options['prefix_tables']) || ($options['prefix_tables'] === true)) {
      array_walk($table, function (&$v) {
        if ((strlen($v) < 7) || (substr($v, 0, 7) != ':table_')) {
          $v = ':table_' . $v;
        }
      }
      );
    }

    if (!is_array($fields)) {
      $fields = [
        $fields
      ];
    }

    if (isset($order) && !is_array($order)) {
      $order = [
        $order
      ];
    }

    if (isset($limit)) {
      if (is_array($limit) && (count($limit) === 2) && is_numeric($limit[0]) && is_numeric($limit[1])) {
        $limit = implode(', ', $limit);
      } elseif (!is_numeric($limit)) {
        $limit = null;
      }
    }

    $statement = 'select ' . implode(', ', $fields) . ' from ' . implode(', ', $table);

    if (!isset($where) && !isset($cache)) {
      if (isset($order)) {
        $statement .= ' order by ' . implode(', ', $order);
      }

      return $this->query($statement);
    }

    if (isset($where)) {
      $statement .= ' where ';

      $counter = 0;

      $it_where = new CachingIterator(new ArrayIterator($where), CachingIterator::TOSTRING_USE_CURRENT);

      foreach ($it_where as $key => $value) {
        if (is_array($value)) {
          if (isset($value['val'])) {
            $statement .= $key . ' ' . ($value['op'] ?? '=') . ' :cond_' . $counter;
          }

          if (isset($value['rel'])) {
            if (isset($value['val'])) {
              $statement .= ' and ';
            }

            if (is_array($value['rel'])) {
              $it_rel = new CachingIterator(new ArrayIterator($value['rel']), CachingIterator::TOSTRING_USE_CURRENT);

              foreach ($it_rel as $rel) {
                $statement .= $key . ' = ' . $rel;

                if ($it_rel->hasNext()) {
                  $statement .= ' and ';
                }
              }
            } else {
              $statement .= $key . ' ' . ($value['op'] ?? '=') . ' ' . $value['rel'];
            }
          }
        } else {
          $statement .= $key . ' = :cond_' . $counter;
        }

        if ($it_where->hasNext()) {
          $statement .= ' and ';
        }

        $counter++;
      }
    }

    if (isset($order)) {
      $statement .= ' order by ' . implode(', ', $order);
    }

    if (isset($limit)) {
      $statement .= ' limit ' . $limit;
    }

    $Q = $this->prepare($statement);

    if (isset($where)) {
      $counter = 0;

      foreach ($it_where as $value) {
        if (is_array($value)) {
          if (isset($value['val'])) {
            $Q->bindValue(':cond_' . $counter, $value['val']);
          }
        } else {
          $Q->bindValue(':cond_' . $counter, $value);
        }

        $counter++;
      }
    }

    if (isset($cache)) {
      if (!is_array($cache)) {
        $cache = [$cache];
      }

      call_user_func_array([$Q, 'setCache'], $cache);
    }

    $Q->execute();

    return $Q;
  }

  /**
   * Saves data to a specified database table. Can perform either an insert or an update operation
   * depending on whether a where condition is provided.
   *
   * @param string $table The name of the database table. If table prefixing is enabled in options,
   *                      the table name will be automatically prefixed.
   * @param array|null $data An associative array of column-value pairs to be inserted/updated.
   *                         Columns as keys, values as data to save.
   */
  public function save(string $table, array|null $data, array|null $where_condition = null, array|null $options = null)
  {
    if (empty($data)) {
      return false;
    }

    if (!isset($options['prefix_tables']) || ($options['prefix_tables'] === true)) {
      if ((strlen($table) < 7) || (substr($table, 0, 7) != ':table_')) {
        $table = ':table_' . $table;
      }
    }

    if (isset($where_condition)) {
      $statement = 'update ' . $table . ' set ';

      foreach ($data as $c => $v) {
        if (is_null($v)) {
          $v = 'null';
        }

        if ($v == 'now()' || $v === 'null') {
          $statement .= $c . ' = ' . $v . ', ';
        } else {
          $statement .= $c . ' = :new_' . $c . ', ';
        }
      }

      $statement = substr($statement, 0, -2) . ' where ';

      foreach (array_keys($where_condition) as $c) {
        $statement .= $c . ' = :cond_' . $c . ' and ';
      }

      $statement = substr($statement, 0, -5);

      $Q = $this->prepare($statement);

      foreach ($data as $c => $v) {
        if ($v != 'now()' && $v !== 'null' && !is_null($v)) {
          $Q->bindValue(':new_' . $c, $v);
        }
      }

      foreach ($where_condition as $c => $v) {
        $Q->bindValue(':cond_' . $c, $v);
      }

      $Q->execute();

      return $Q->rowCount();
    } else {
      $is_prepared = false;

      $statement = 'insert into ' . $table . ' (' . implode(', ', array_keys($data)) . ') values (';

      foreach ($data as $c => $v) {
        if (is_null($v)) {
          $v = 'null';
        }

        if ($v == 'now()' || $v === 'null') {
          $statement .= $v . ', ';
        } else {
          if ($is_prepared === false) {
            $is_prepared = true;
          }

          $statement .= ':' . $c . ', ';
        }
      }

      $statement = substr($statement, 0, -2) . ')';

      if ($is_prepared === true) {
        $Q = $this->prepare($statement);

        foreach ($data as $c => $v) {
          if ($v != 'now()' && $v !== 'null' && !is_null($v)) {
            $Q->bindValue(':' . $c, $v);
          }
        }

        $Q->execute();

        return $Q->rowCount();
      } else {
        return $this->exec($statement);
      }
    }
  }

  /**
   * Deletes records from the specified table based on the provided where conditions.
   *
   * @param string $table The name of the table from which records should be deleted.
   * @param array $where_condition An associative array representing the WHERE conditions,
   *                                where the keys are column names and the values are the corresponding values to match.
   *                                If the array is empty, all rows in the table will be deleted.
   * @param array|null $options Optional settings. If the 'prefix_tables'
   */
  public function delete(string $table, array $where_condition = [], ?array $options = null): int
  {
    if (!isset($options['prefix_tables']) || ($options['prefix_tables'] === true)) {
      if ((strlen($table) < 7) || (substr($table, 0, 7) != ':table_')) {
        $table = ':table_' . $table;
      }
    }

    $statement = 'delete from ' . $table;

    if (empty($where_condition)) {
      return $this->exec($statement);
    }

    $statement .= ' where ';

    foreach (array_keys($where_condition) as $c) {
      $statement .= $c . ' = :cond_' . $c . ' and ';
    }

    $statement = substr($statement, 0, -5);

    $Q = $this->prepare($statement);

    foreach ($where_condition as $c => $v) {
      $Q->bindValue(':cond_' . $c, $v);
    }

    $Q->execute();

    return $Q->rowCount();
  }

  /**
   * Imports SQL queries from a given SQL file into the database.
   *
   * @param string $sql_file Path to the SQL file to be imported.
   * @param string|null $table_prefix Optional table prefix to be used for renaming tables in the SQL file.
   * @return bool Returns true if all SQL queries are successfully executed, otherwise false.
   */
  public function importSQL(string $sql_file, ?string $table_prefix = null): bool
  {
    try {
      if (is_file($sql_file)) {
        $import_queries = file_get_contents($sql_file);

        if ($import_queries === false) {
          throw new Exception('CLICSHOPPING\Db::importSQL(): Cannot read SQL import file: ' . $sql_file);
        }
      } else {
        throw new Exception('CLICSHOPPING\Db::importSQL(): SQL import file does not exist: ' . $sql_file);
      }
    } catch (Exception $e) {
      trigger_error($e->getMessage());

      return false;
    }

    set_time_limit(0);

    $sql_queries = [];
    $sql_length = strlen($import_queries);
    $pos = strpos($import_queries, ';');

    for ($i = $pos; $i < $sql_length; $i++) {
// remove comments
      if ((substr($import_queries, 0, 1) == '#') || (substr($import_queries, 0, 2) == '--')) {
        $import_queries = ltrim(substr($import_queries, strpos($import_queries, "\n")));
        $sql_length = strlen($import_queries);
        $i = strpos($import_queries, ';') - 1;
        continue;
      }

      if (substr($import_queries, $i + 1, 1) == "\n") {
        $next = '';

        for ($j = ($i + 2); $j < $sql_length; $j++) {
          if (!empty(substr($import_queries, $j, 1))) {
            $next = substr($import_queries, $j, 6);

            if ((substr($next, 0, 1) == '#') || (substr($next, 0, 2) == '--')) {
// find out where the break position is so we can remove this line (#comment line)
              for ($k = $j; $k < $sql_length; $k++) {
                if (substr($import_queries, $k, 1) == "\n") {
                  break;
                }
              }

              $query = substr($import_queries, 0, $i + 1);

              $import_queries = substr($import_queries, $k);

// join the query before the comment appeared, with the rest of the dump
              $import_queries = $query . $import_queries;
              $sql_length = strlen($import_queries);
              $i = strpos($import_queries, ';') - 1;
              continue 2;
            }

            break;
          }
        }

        if (empty($next)) { // get the last insert query
          $next = 'insert';
        }

        if ((mb_strtoupper($next) == 'DROP T') ||
          (mb_strtoupper($next) == 'CREATE') ||
          (mb_strtoupper($next) == 'INSERT') ||
          (mb_strtoupper($next) == 'ALTER') ||
          (mb_strtoupper($next) == 'SET FO')) {
          $next = '';

          $sql_query = substr($import_queries, 0, $i);

          if (isset($table_prefix) && !empty($table_prefix)) {
            if (mb_strtoupper(substr($sql_query, 0, 20)) == 'DROP TABLE IF EXISTS') {
              $sql_query = 'DROP TABLE IF EXISTS ' . $table_prefix . substr($sql_query, 21);
            } elseif (mb_strtoupper(substr($sql_query, 0, 12)) == 'CREATE TABLE') {
              $sql_query = 'CREATE TABLE ' . $table_prefix . substr($sql_query, 13);
            } elseif (mb_strtoupper(substr($sql_query, 0, 11)) == 'INSERT INTO') {
              $sql_query = 'INSERT INTO ' . $table_prefix . substr($sql_query, 12);
            } elseif (mb_strtoupper(substr($sql_query, 0, 12)) == 'CREATE INDEX') {
              $sql_query = substr($sql_query, 0, stripos($sql_query, ' on ')) .
                ' on ' .
                $table_prefix .
                substr($sql_query, stripos($sql_query, ' on ') + 4);
            }
          }

          $sql_queries[] = trim($sql_query);

          $import_queries = ltrim(substr($import_queries, $i + 1));
          $sql_length = strlen($import_queries);
          $i = strpos($import_queries, ';') - 1;
        }
      }
    }

    $error = false;

    foreach ($sql_queries as $q) {
      if ($this->exec($q) === false) {
        $error = true;

        break;
      }
    }

    return !$error;
  }

  /**
   * Parses a schema definition file and generates an array representation of the database schema.
   *
   * @param string $file The path to the schema definition file. It is expected to have a structured format
   *                     where different sections (columns, indexes, foreign keys, properties) are divided
   *                     by*/
  public static function getSchemaFromFile(string $file): array
  {
    $table = substr(basename($file), 0, strrpos(basename($file), '.'));

    $schema = [
      'name' => $table
    ];

    $is_index = $is_foreign = $is_property = false;

    foreach (file($file) as $row) {
      $row = trim($row);

      if (!empty($row)) {
        if ($row == '--') {
          $is_index = true;
          $is_foreign = $is_property = false;

          continue;
        } elseif ($row == '==') {
          $is_foreign = true;
          $is_index = $is_property = false;

          continue;
        } elseif ($row == '##') {
          $is_property = true;
          $is_index = $is_foreign = false;

          continue;
        }

        $details = str_getcsv($row, ' ');

        $field_name = array_shift($details);

        if ($is_index === true) {
          $schema['index'][$field_name] = $details;

          continue;
        } elseif ($is_foreign === true) {
          foreach ($details as $d) {
            if (!str_contains($d, '(')) {
              $schema['foreign'][$field_name]['col'][] = $d;

              continue;
            }

            if (preg_match('/(.*)\((.*)\)/', $d, $info)) {
              switch ($info[1]) {
                case 'ref_table':
                case 'on_delete':
                case 'on_update':
                case 'prefix':
                  $schema['foreign'][$field_name][$info[1]] = $info[2];

                  break;

                case 'ref_col':
                  $schema['foreign'][$field_name]['ref_col'] = explode(' ', $info[2]);
                  break;
              }
            }
          }

          continue;
        } elseif ($is_property === true) {
          switch ($field_name) {
            case 'engine':
              $schema['property']['engine'] = implode(' ', $details);
              break;

            case 'character_set':
              $schema['property']['character_set'] = implode(' ', $details);
              break;

            case 'collate':
              $schema['property']['collate'] = implode(' ', $details);
              break;
          }

          continue;
        }

        $field_type = array_shift($details);

        if (preg_match('/(.*)\((.*)\)/', $field_type, $type_details)) {
          $schema['col'][$field_name]['type'] = $type_details[1];
          $schema['col'][$field_name]['length'] = $type_details[2];
        } else {
          $schema['col'][$field_name]['type'] = $field_type;
        }

        if (preg_match('/default\((.*)\)/', implode(' ', $details), $type_default)) {
          $schema['col'][$field_name]['default'] = $type_default[1];

          $default_pos = array_search('default(' . $type_default[1] . ')', $details);
          array_splice($details, $default_pos, 1);
        }

        $is_binary = array_search('binary', $details);

        if (is_integer($is_binary)) {
          array_splice($details, $is_binary, 1);
          $schema['col'][$field_name]['binary'] = true;
        }

        $is_unsigned = array_search('unsigned', $details);

        if (is_integer($is_unsigned)) {
          array_splice($details, $is_unsigned, 1);
          $schema['col'][$field_name]['unsigned'] = true;
        }

        $is_not_null = array_search('not_null', $details);

        if (is_integer($is_not_null)) {
          array_splice($details, $is_not_null, 1);
          $schema['col'][$field_name]['not_null'] = true;
        }

        $is_auto_increment = array_search('auto_increment', $details);

        if (is_integer($is_auto_increment)) {
          array_splice($details, $is_auto_increment, 1);
          $schema['col'][$field_name]['auto_increment'] = true;
        }

        if (!empty($details)) {
          $schema['col'][$field_name]['other'] = implode(' ', $details);
        }
      }
    }

    return $schema;
  }

  /**
   * Generates an SQL "CREATE TABLE" statement from the provided schema definition.
   *
   * @param array $schema The table schema, including table name, columns, indexes, and other attributes.
   *                       - 'name' (string): The name of the table.
   *                       - 'col' (array): An associative array of column definitions, where the key is the column name and the value is an array of column properties:
   *                           - 'type' (string): Data type of the column.
   *                           - 'length' (
   */
  public static function getSqlFromSchema(array $schema, ?string $prefix = null)
  {
    $sql = 'CREATE TABLE ' . (isset($prefix) ? $prefix : '') . $schema['name'] . ' (' . "\n";

    $rows = [];

    foreach ($schema['col'] as $name => $fields) {
      $row = '  ' . $name . ' ' . $fields['type'];

      if (isset($fields['length'])) {
        $row .= '(' . $fields['length'] . ')';
      }

      if (isset($fields['binary']) && ($fields['binary'] === true)) {
        $row .= ' binary';
      }

      if (isset($fields['unsigned']) && ($fields['unsigned'] === true)) {
        $row .= ' unsigned';
      }

      if (isset($fields['default'])) {
        $row .= ' DEFAULT ' . $fields['default'];
      }

      if (isset($fields['not_null']) && ($fields['not_null'] === true)) {
        $row .= ' NOT NULL';
      }

      if (isset($fields['auto_increment']) && ($fields['auto_increment'] === true)) {
        $row .= ' auto_increment';
      }

      $rows[] = $row;
    }

    if (isset($schema['index'])) {
      foreach ($schema['index'] as $name => $fields) {
        if ($name == 'primary') {
          $name = 'PRIMARY KEY';
        } else {
          $name = 'KEY ' . $name;
        }

        $row = '  ' . $name . ' (' . implode(', ', $fields) . ')';

        $rows[] = $row;
      }
    }

    if (isset($schema['foreign'])) {
      foreach ($schema['foreign'] as $name => $fields) {
        $row = '  FOREIGN KEY ' . $name . ' (' . implode(', ', $fields['col']) . ') REFERENCES ' . (isset($prefix) && (!isset($fields['prefix']) || ($fields['prefix'] != 'false')) ? $prefix : '') . $fields['ref_table'] . '(' . implode(', ', $fields['ref_col']) . ')';

        if (isset($fields['on_update'])) {
          $row .= ' ON UPDATE ' . mb_strtoupper($fields['on_update']);
        }

        if (isset($fields['on_delete'])) {
          $row .= ' ON DELETE ' . mb_strtoupper($fields['on_delete']);
        }

        $rows[] = $row;
      }
    }

    $sql .= implode(',' . "\n", $rows) . "\n" . ')';

    if (isset($schema['property'])) {
      if (isset($schema['property']['engine'])) {
        $sql .= ' ENGINE ' . $schema['property']['engine'];
      }

      if (isset($schema['property']['character_set'])) {
        $sql .= ' CHARACTER SET ' . $schema['property']['character_set'];
      }

      if (isset($schema['property']['collate'])) {
        $sql .= ' COLLATE ' . $schema['property']['collate'];
      }
    }

    $sql .= ';';

    return $sql;
  }

  /**
   * Prepares and sanitizes the input by processing strings or arrays recursively.
   *
   * @param string|array $string The input string or array to be sanitized.
   * @return string|array The sanitized string or array.
   */
  public static function prepareInput(string $string): string
  {
    if (is_string($string)) {
      return HTML::sanitize($string);
    } elseif (is_array($string)) {
      foreach ($string as $k => $v) {
        $string[$k] = static::prepareInput($v);
      }

      return $string;
    } else {
      return $string;
    }
  }

  /**
   * Escapes and formats a string to be used as an identifier in a database query.
   *
   * @param string $string The input string to be formatted as an identifier.
   * @return string The formatted identifier with special characters escaped.
   */
  public static function prepareIdentifier(string $string): string
  {
    return '`' . str_replace('`', '``', $string) . '`';
  }

  /**
   * Sets the prefix to be used for table names.
   *
   * @param string $prefix The prefix to be set for table names.
   * @return void
   */
  public function setTablePrefix(string $prefix)
  {
    $this->table_prefix = $prefix;
  }


  /**
   * Automatically prefixes table names in a database query statement with the configured table prefix.
   *
   * @param string $statement The SQL query statement containing placeholders for table names.
   * @return string The SQL query statement with table name placeholders replaced by the configured prefix.
   */
  protected function autoPrefixTables(string $statement): string
  {
    $prefix = '';

    if (isset($this->table_prefix)) {
      $prefix = $this->table_prefix;
    } elseif (CLICSHOPPING::configExists('db_table_prefix')) {
      $prefix = CLICSHOPPING::getConfig('db_table_prefix');
    }

    $statement = str_replace(':table_', $prefix, $statement);

    return $statement;
  }

  /**
   * Calculates the total size of the database in megabytes.
   *
   * @return float The size of the database rounded to one decimal place in megabytes.
   */
  public static function sizeDb(): float
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qresult = $CLICSHOPPING_Db->query('SHOW table status FROM ' . CLICSHOPPING::getConfig('db_database'));

    $size = 0;

    while ($Qresult->fetch()) {
      $size += $Qresult->value('Data_length');
    }

    $size_db = round(($size / 1024) / 1024, 1);

    return $size_db;
  }

  /**
   * Calculates and displays the size of the database in megabytes.
   *
   * @return float The size of the database in megabytes, rounded to one decimal place.
   */
  public static function displayDbSize(): float
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qresult = $CLICSHOPPING_Db->query('show table status from ' . CLICSHOPPING::getConfig('db_database'));

    $size = 0;

    while ($Qresult->fetch()) {
      $size .= $Qresult->value('Name') . ': ' . round(($Qresult->valueDecimal('Data_length') / 1024) / 1024, 4) . '<br />\n';
    }

    $size_db = round(($size / 1024) / 1024, 1);

    return $size_db;
  }

  /**
   * Installs a new database schema from the given filename.
   *
   * @param string $filename The name of the file containing the database schema to install, excluding the ".txt" extension.
   * @param bool|null $migrate Optional. Indicates whether to use the migration directory for schema files. Defaults to false.
   * @return void
   */
  public function installNewDb(string $filename, ?bool $migrate = false): void
  {
    $prefix = CLICSHOPPING::getConfig('db_table_prefix');

    $this->exec('SET FOREIGN_KEY_CHECKS = 0');

    if ($migrate === true) {
      $directory = CLICSHOPPING::BASE_DIR . 'Custom/Schema/' . CLICSHOPPING::getVersionDirectory() . DIRECTORY_SEPARATOR;
    } else {
      $directory = CLICSHOPPING::BASE_DIR . 'Custom/Schema/';
    }

    $path_file = $directory . $filename . '.txt';
    $file = $directory . $filename;

    if (is_file($path_file)) {
      $schema = $this->getSchemaFromFile($path_file);
      $sql = $this->getSqlFromSchema($schema, $prefix);

      $this->exec('DROP TABLE IF EXISTS ' . $prefix . basename($file, '.txt'));

      $this->exec($sql);
      $this->importSQL($path_file, $prefix);

      $this->exec('SET FOREIGN_KEY_CHECKS = 1');

      Cache::clear('configuration');
    }
  }
}
