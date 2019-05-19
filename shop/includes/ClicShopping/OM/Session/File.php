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

  use ClicShopping\OM\CLICSHOPPING;

  class File extends \ClicShopping\OM\SessionAbstract implements \SessionHandlerInterface
  {
    protected $path;

    public function __construct()
    {

      if (!is_dir(CLICSHOPPING::BASE_DIR . 'Work/Session')) {
        mkdir(CLICSHOPPING::BASE_DIR . 'Work/Session', 0777, true);
      }

      $this->setSavePath(CLICSHOPPING::BASE_DIR . 'Work/Session');

      session_set_save_handler($this, true);
    }

    /**
     * Checks if a session exists
     *
     * @param string $id The ID of the session
     */
    public function exists($session_id)
    {
      $id = basename($session_id);

      return is_file($this->path . '/sess_' . $id);
    }

    public function open($save_path, $name)
    {
      if (!is_dir($save_path)) {
        mkdir($save_path, 0777);
      }

      return true;
    }

    public function close()
    {
      return true;
    }

    public function read($session_id)
    {
      $id = basename($session_id);

      $result = false;


      if ($this->exists($id)) {
        $result = file_get_contents($this->path . '/sess_' . $id);
      }

      if ($result === false) {
        $result = '';
      }

      return $result;
    }

    public function write($session_id, $session_data)
    {
      $id = basename($session_id);

      return file_put_contents($this->path . '/sess_' . $id, $session_data) === false ? false : true;
    }

    /**
     * Deletes the session data from the file storage handler
     *
     * @param string $session_id The ID of the session
     */

    public function destroy($session_id)
    {
      $id = basename($session_id);

      if ($this->exists($id)) {
        return unlink($this->path . '/sess_' . $id);
      }

      return true;
    }

    public function gc($maxlifetime)
    {
      foreach (glob($this->path . '/sess_*') as $file) {
        if (filemtime($file) + $maxlifetime < time()) {
          unlink($file);
        }
      }

      return true;
    }

    /**
     * Sets the storage location for the file storage handler
     *
     * @param string $path The file path to store the session data in
     */
    public function setSavePath($path)
    {
      if ((strlen($path) > 1) && (substr($path, -1) == '/')) {
        $path = substr($path, 0, -1);
      }

      session_save_path($path);

      $this->path = session_save_path();
    }
  }
