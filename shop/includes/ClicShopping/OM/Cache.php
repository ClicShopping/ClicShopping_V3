<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\OM;

  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\CLICSHOPPING;

  class Cache {
    protected static $path;

    protected $key;
    protected $data;

    public function __construct($key) {

      $this->setPath();

      $this->setKey($key);
    }

    public function setKey($key) {
      if (!$this->hasSafeName($key)) {
          trigger_error('ClicShopping\\OM\\Cache: Invalid key name (\'' . $key . '\'). Valid characters are a-zA-Z0-9-_');

          return false;
      }

      $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function save($data) {

      if (FileSystem::isWritable(static::getPath())) {
        return file_put_contents(static::getPath() . $this->key . '.cache', serialize($data), LOCK_EX) !== false;
      }

      return false;
    }

    public function exists($expire = null) {

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
 *
 * @access public
 * @return mixed
 */
    public function get()
    {

        $filename = static::getPath() . $this->key . '.cache';
        if (is_file($filename)) {
            $this->data = unserialize(file_get_contents($filename));
        }

        return $this->data;
    }

    public static function hasSafeName($key)
    {
      return preg_match('/^[a-zA-Z0-9-_]+$/', $key) === 1;
    }

    public function getTime()
    {
        $filename = static::getPath() . $this->key . '.cache';
        if (is_file($filename)) {
            return filemtime($filename);
        }

        return false;
    }

    public static function find($key, $strict = true) {

        if (!static::hasSafeName($key)) {
            trigger_error('ClicShopping\\OM\\Cache::find(): Invalid key name (\'' . $key . '\'). Valid characters are a-zA-Z0-9-_');

            return false;
        }

        if (is_file(static::getPath(). $key . '.cache')) {
          return true;
        }

        if ($strict === false) {
            $key_length = strlen($key);

            $d = dir(static::getPath());

            while (($entry = $d->read()) !== false) {
                if ((strlen($entry) >= $key_length) && (substr($entry, 0, $key_length) == $key)) {
                    $d->close();

                    return true;
                }
            }
        }

        return false;
    }

    public static function setPath()
    {
        static::$path = CLICSHOPPING::BASE_DIR . 'Work/Cache/';
    }

    public static function getPath()
    {
        if (!isset(static::$path)) {
            static::setPath();
        }

        return static::$path;
    }

/**
 * Delete cached files by their key ID
 *
 * @param string $key The key ID of the cached files to delete
 * @access public
 */
    public static function clear($key) {
      if (!static::hasSafeName($key)) {
        trigger_error('ClicShopping\\OM\\Cache::clear(): Invalid key name (\'' . $key . '\'). Valid characters are a-zA-Z0-9-_');

        return false;
      }

      if (FileSystem::isWritable(static::getPath())) {
        foreach (glob(static::getPath() . $key . '*.cache') as $c) {
          unlink($c);
        }
      }
    }

    public static function clearAll()
    {
      if (FileSystem::isWritable(static::getPath())) {
        foreach (glob(static::getPath() . '*.cache') as $c) {
          unlink($c);
        }
      }
    }
  }
