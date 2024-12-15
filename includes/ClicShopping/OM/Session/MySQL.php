<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Session;

use ClicShopping\OM\Registry;

/**
 * The MySQL class provides a session handler implementation
 * that interacts with a database to store and manage session data.
 * It implements the SessionHandlerInterface and extends the
 * SessionAbstract class to define custom session management operations.
 */
class MySQL extends \ClicShopping\OM\SessionAbstract implements \SessionHandlerInterface
{
  private mixed $db;

  /**
   * Constructor method for initializing session handler.
   *
   * Initializes the database connection from the registry and registers
   * the current instance as the session save handler.
   *
   * @return void
   */
  public function __construct()
  {
    $this->db = Registry::get('Db');

    session_set_save_handler($this, true);
  }

  /**
   * Checks if a session with the given session ID exists in the database.
   *
   * @param string $session_id The session ID to check for existence.
   * @return bool True if the session exists, otherwise false.
   */
  public function exists(string $session_id): bool
  {
    $Qsession = $this->db->prepare('select 1 from :table_sessions where sesskey = :sesskey');
    $Qsession->bindValue(':sesskey', $session_id);
    $Qsession->execute();

    return $Qsession->fetch() !== false;
  }

  /**
   * Opens a session.
   *
   * @param string $save_path The path where the session is stored.
   * @param string $name The name of the session.
   * @return bool Returns true on success.
   */
  public function open(string $save_path, string $name): bool
  {
    return true;
  }

  /**
   * Closes the session.
   *
   * @return bool Returns true on success.
   */
  public function close(): bool
  {
    return true;
  }

  /**
   * Reads the session data associated with the given session ID.
   *
   * @param string $session_id A unique identifier for the session to be read.
   * @return string Returns the session data as a string if found, otherwise returns an empty string.
   */
  public function read(string $session_id): string
  {
    $Qsession = $this->db->prepare('select value from :table_sessions where sesskey = :sesskey');
    $Qsession->bindValue(':sesskey', $session_id);
    $Qsession->execute();

    if ($Qsession->fetch() !== false) {
      return $Qsession->value('value');
    }
    return '';
  }

  /**
   * Writes session data to the storage.
   *
   * @param string $session_id The session ID.
   * @param string $session_data The session data to be written.
   * @return bool Returns true on success or false on failure.
   */
  public function write(string $session_id, string $session_data): bool
  {
    if ($this->exists($session_id)) {
      $result = $this->db->save('sessions', [
        'expiry' => time(),
        'value' => $session_data
      ], [
        'sesskey' => $session_id
      ]);
    } else {
      $result = $this->db->save('sessions', [
        'sesskey' => $session_id,
        'expiry' => time(),
        'value' => $session_data
      ]);
    }

    return $result !== false;
  }

  /**
   * Destroys a session associated with the given session ID.
   *
   * @param string $session_id The ID of the session to be destroyed.
   * @return bool True if the session was successfully destroyed, false otherwise.
   */
  public function destroy(string $session_id): bool
  {
    $result = $this->db->delete('sessions', [
      'sesskey' => $session_id
    ]);

    return $result !== false;
  }

  /**
   * Removes expired sessions from the database based on the maximum lifetime parameter.
   *
   * @param int $maxlifetime The maximum lifetime in seconds for session validity.
   * @return int|false Returns the number of rows deleted as an integer on success, or false on failure.
   */
  public function gc($maxlifetime): int|false
  {
    $Qdel = $this->db->prepare('delete from :table_sessions where expiry < :expiry');
    $Qdel->bindValue(':expiry', time() - $maxlifetime);
    $Qdel->execute();

    return $Qdel->isError() === false;
  }
}
