<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM;

  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\CLICSHOPPING;

  class Cache
  {
    protected static string $path;
    protected const SAFE_KEY_NAME_REGEX = 'a-zA-Z0-9-_';
    protected string $key;
    protected $data;

    public function __construct(string $key)
    {
      static::setPath();

      $this->setKey($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function setKey(string $key)
    {
      if (!static::hasSafeName($key)) {
        trigger_error('ClicShopping\\OM\\Cache: Invalid key name ("' . $key . '"). Valid characters are ' . static::SAFE_KEY_NAME_REGEX);

        return false;
      }

      $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
      return $this->key;
    }

    /**
     * @param string $data
     * @return bool
     */
    public function save($data)
    {
      if (FileSystem::isWritable(static::getPath())) {
        return file_put_contents(static::getPath() . $this->key . '.cache', serialize($data), LOCK_EX) !== false;
      }

      return false;
    }

    /**
     * @param string|null $expire
     * @return bool
     */
    public function exists(?string $expire = null): bool
    {

      $filename = static::getPath() . $this->key . '.cache';

      if (is_file($filename)) {
      
        if (!isset($expire)) {
          return true;
        }

        $difference = floor((time() - filemtime($filename)) / 60);

        if (is_numeric($expire) && ($difference < $expire)) {
          return true;
        }
      }

      return false;
    }

    /**
     * Return the cached data
     * @return array
     */
    public function get()
    {
      $filename = static::getPath() . $this->key . '.cache';
      
      if (is_file($filename)) {
        $this->data = unserialize(file_get_contents($filename));
      }

      return $this->data;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function hasSafeName(string $key): bool
    {
      return preg_match('/^[' . static::SAFE_KEY_NAME_REGEX . ']+$/', $key) === 1;
    }

    /**
     * @return bool
     */
    public function getTime()
    {
      $filename = static::getPath() . $this->key . '.cache';
      if (is_file($filename)) {
        return filemtime($filename);
      }

      return false;
    }

    /**
     * @param string $key
     * @param bool $strict
     * @return bool
     */
    public static function find(string $key, bool $strict = true): bool
    {

      if (!static::hasSafeName($key)) {
        trigger_error('ClicShopping\\OM\\Cache::find(): Invalid key name (\'' . $key . '\'). Valid characters are a-zA-Z0-9-_');

        return false;
      }

      if (is_file(static::getPath() . $key . '.cache')) {
        return true;
      }

      if ($strict === false) {
        $key_length = \strlen($key);

        $d = dir(static::getPath());

        while (($entry = $d->read()) !== false) {
          if ((\strlen($entry) >= $key_length) && (substr($entry, 0, $key_length) == $key)) {
            $d->close();

            return true;
          }
        }
      }

      return false;
    }

    /**
     *
     */
    public static function setPath()
    {
      static::$path = CLICSHOPPING::BASE_DIR . 'Work/Cache/';
    }

    /**
     * @return mixed
     */
     public static function getPath()
    {
      if (!isset(static::$path)) {
        static::setPath();
      }

      return static::$path;
    }

     /**
     * Delete cached files by their key ID
     * @param string $key The key ID of the cached files to delete
     * @return bool
     */
    public static function clear(string $key)
    {
      $key = basename($key);

      if (!static::hasSafeName($key)) {
        trigger_error('ClicShopping\\Cache::clear(): Invalid key name ("' . $key . '"). Valid characters are ' . static::SAFE_KEY_NAME_REGEX);

        return false;
      }

      if (FileSystem::isWritable(static::getPath())) {
        $key_length = \strlen($key);

        $DLcache = new DirectoryListing(static::getPath());
        $DLcache->setIncludeDirectories(false);

        foreach ($DLcache->getFiles() as $file) {
          if ((\strlen($file['name']) >= $key_length) && (substr($file['name'], 0, $key_length) == $key)) {
            unlink(static::getPath() . $file['name']);
          }
        }
      }
    }

    /**
     * Clear all cache
     */
    public static function clearAll()
    {
      if (FileSystem::isWritable(static::getPath())) {
        foreach (glob(static::getPath() . '*.cache', GLOB_NOSORT) as $c) {
          unlink($c);
        }
      }
    }
  }
