<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM\Session;

  use ClicShopping\OM\Registry;

  class MySQL extends \ClicShopping\OM\SessionAbstract implements \SessionHandlerInterface
  {
    protected mixed $db;

    public function __construct()
    {
      $this->db = Registry::get('Db');

      session_set_save_handler($this, true);
    }

    /**
     * Checks if a session exists
     *
     * @param string $session_id The ID of the session
     */
    public function exists(string $session_id) :bool
    {
      $Qsession = $this->db->prepare('select 1 from :table_sessions where sesskey = :sesskey');
      $Qsession->bindValue(':sesskey', $session_id);
      $Qsession->execute();

      return $Qsession->fetch() !== false;
    }

    /**
     * Opens the database storage handler
     */
    public function open($save_path, $name) :bool
    {
      return true;
    }

    /**
     * Closes the database storage handler
     */
    public function close(): bool
    {
      return true;
    }

    /**
     * Read session data from the database storage handler
     *
     * @param string $session_id The ID of the session
     */
    public function read($session_id) :string
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
     * Writes session data to the database storage handler
     *
     * @param string $session_id The ID of the session
     * @param string $session_data The session data to store
     */
    public function write($session_id, $session_data): bool
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
     * Deletes the session data from the database storage handler
     *
     * @param string $session_id The ID of the session
     */
    public function destroy($session_id) :bool
    {
      $result = $this->db->delete('sessions', [
        'sesskey' => $session_id
      ]);

      return $result !== false;
    }

    /**
     * Garbage collector for the database storage handler
     * @param int $maxlifetime
     * @return bool
     * The maxmimum time a session should exist
     */
    public function gc($maxlifetime) :bool
    {
      $Qdel = $this->db->prepare('delete from :table_sessions where expiry < :expiry');
      $Qdel->bindValue(':expiry', time() - $maxlifetime);
      $Qdel->execute();

      return $Qdel->isError() === false;
    }
  }
