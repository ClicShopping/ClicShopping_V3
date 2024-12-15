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

use function strlen;


/**
 * Class Cache
 * Manages caching operations including saving, retrieving, checking existence, and clearing cache data.
 */
class Cache
{
  protected static string $path;
  protected const SAFE_KEY_NAME_REGEX = 'a-zA-Z0-9-_';
  protected string $key;
  protected $data;

  /**
   * Constructor method for initializing the class.
   *
   * @param string $key A unique identifier key to set during the object instantiation.
   * @return void
   */
  public function __construct(string $key)
  {
    static::setPath();

    $this->setKey($key);
  }

  /**
   * Sets the cache key if it matches the valid key name pattern.
   *
   * @param string $key The cache key to set. It must comply with the valid naming convention.
   * @return bool Returns false if the key name is invalid; otherwise, no value is returned.
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
   * Retrieves the key value.
   *
   * @return mixed The value of the key property.
   */
  public function getKey()
  {
    return $this->key;
  }

  /**
   * Saves the provided data to a cache file if the directory is writable.
   *
   * @param mixed $data The data to be saved, which will be serialized and written to the cache file.
   * @return bool Returns true if the data was successfully written to the cache file, otherwise false.
   */
  public function save($data)
  {
    if (FileSystem::isWritable(static::getPath())) {
      return file_put_contents(static::getPath() . $this->key . '.cache', serialize($data), LOCK_EX) !== false;
    }

    return false;
  }

  /**
   * Checks if the cache file exists and optionally verifies if it has not expired.
   *
   * @param string|null $expire Optional expiration time in minutes. If provided, checks if the cache file's age is less than the given value.
   * @return bool Returns true if the cache file exists and meets the expiration criteria (if provided), otherwise false.
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
   * Retrieves the cached data associated with the current key.
   *
   * This method constructs the filename based on the cache path and the key.
   * If the file exists, it reads and unserializes its contents into the data property.
   * Finally, it returns the retrieved data.
   *
   * @return mixed Returns the cached data if it exists, or null if no cache is found.
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
   * Checks if the provided key has a safe name based on a predefined regex pattern.
   *
   * @param string $key The key to be checked.
   * @return bool Returns true if the key matches the safe name criteria, false otherwise.
   */
  public static function hasSafeName(string $key): bool
  {
    return preg_match('/^[' . static::SAFE_KEY_NAME_REGEX . ']+$/', $key) === 1;
  }

  /**
   * Retrieves the last modification time of the cache file associated with the current key.
   *
   * @return int|false The file modification time as a Unix timestamp if the file exists, or false if the file does not exist.
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
   * Finds whether a cache file exists for the given key.
   *
   * @param string $key The cache key to search for. Must consist of valid characters (a-zA-Z0-9-_).
   * @param bool $strict If true, an exact match is required for the key. If false, a partial match is allowed.
   * @return bool Returns true if a matching cache file is found, otherwise false.
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

  /**
   * Sets the path to the cache directory.
   *
   * @return void
   */
  public static function setPath()
  {
    static::$path = CLICSHOPPING::BASE_DIR . 'Work/Cache/';
  }

  /**
   * Retrieves the path. If the path is not set, it initializes the path by calling setPath().
   *
   * @return string The current stored path.
   */
  public static function getPath()
  {
    if (!isset(static::$path)) {
      static::setPath();
    }

    return static::$path;
  }

  /**
   * Clears cached files associated with the specified key.
   *
   * @param string $key The key identifying cached files to be cleared. Only safe key names are allowed.
   * @return bool Returns true if the cache path is writable and the operation is performed; false otherwise.
   */
  public static function clear(string $key)
  {
    $key = basename($key);

    if (!static::hasSafeName($key)) {
      trigger_error('ClicShopping\\Cache::clear(): Invalid key name ("' . $key . '"). Valid characters are ' . static::SAFE_KEY_NAME_REGEX);

      return false;
    }

    if (FileSystem::isWritable(static::getPath())) {
      $key_length = strlen($key);

      $DLcache = new DirectoryListing(static::getPath());
      $DLcache->setIncludeDirectories(false);

      foreach ($DLcache->getFiles() as $file) {
        if ((strlen($file['name']) >= $key_length) && (substr($file['name'], 0, $key_length) == $key)) {
          unlink(static::getPath() . $file['name']);
        }
      }
    }
  }

  /**
   * Clears all cache files in the specified directory.
   *
   * @return void
   */
  public static function clearAll(): void
  {
    if (FileSystem::isWritable(static::getPath())) {
      foreach (glob(static::getPath() . '*.cache', GLOB_NOSORT) as $c) {
        unlink($c);
      }
    }
  }
}
