<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 * @info : https://github.com/DarkGhostHunter/Preloader
 *
 */

namespace ClicShopping\OM;

use DarkGhostHunter\Preloader\Preloader;
use SplFileObject;
use function func_get_args;
use function in_array;

/**
 * This class manages the preloading functionality, including checking system requirements,
 * scanning directories for files, and executing the preloading process. It provides support
 * for recursive directory scans and file filtering by extensions.
 */
class Preload
{
  private static string $work_dir = CLICSHOPPING::BASE_DIR . 'Work/Log/';
  private static string $base_dir_autoload = CLICSHOPPING::BASE_DIR . 'External/vendor/autoload.php';
  private static string $output_dir = CLICSHOPPING::BASE_DIR . 'Work/Log/preloader.php';
  private static array $files;
  private static bool $recursive;
  private static $ext_filter;
  private static array $directories;

  /**
   * Checks the current environment and configuration to ensure all required conditions are met.
   *
   * The method verifies if the working directory is writable, the PHP version meets the minimum requirement,
   * and preloading configuration is set to the necessary state.
   *
   * @return bool Returns true if all conditions are satisfied; otherwise, returns false.
   */
  public static function check(): bool
  {
    $result = true;

    if (!is_writable(static::$work_dir)) {
      $result = false;
    }

    if (PHP_VERSION < '7.4.3') {
      $result = false;
    }

    if (CONFIGURATION_PRELOADING == 'false') {
      $result = false;
    }

    return $result;
  }

  /**
   * Executes the preloading process.
   * It first validates the conditions via a check and then proceeds to create a Preloader instance,
   * which is configured to require files from the base directory and write the output to a specific directory.
   *
   * @return void
   */
  public static function execute()
  {
    if (static::check() === true) {
      Preloader::make()
        ->useRequire(static::$base_dir_autoload)
        ->writeTo(static::$output_dir);
    }
  }

  /**
   * Scans the provided filesystem paths and retrieves a list of files based on the specified parameters.
   *
   * This function accepts a path string or an array of path strings, optionally filters files by their extensions,
   * and allows recursive scanning of sub-directories if specified.
   *
   * @return array The list of files retrieved from the specified paths.
   */
  public static function scan(): array
  {
    self::$recursive = false;
    self::$directories = [];
    self::$files = [];
    self::$ext_filter = false;

// Check we have minimum parameters
    if (!$args = func_get_args()) {
      die('Must provide a path string or array of path strings');
    }

    if (gettype($args[0]) != 'string' && gettype($args[0]) != 'array') {
      die('Must provide a path string or array of path strings');
    }

    // Check if recursive scan | default action: no sub-directories
    if (isset($args[2]) && $args[2] === true) {
      self::$recursive = true;
    }

    // Was a filter on file extensions included? | default action: return all file types
    if (isset($args[1])) {
      if (gettype($args[1]) == 'array') {
        self::$ext_filter = array_map('mb_strtolower', $args[1]);
      } else
        if (gettype($args[1]) == 'string') {
          self::$ext_filter[] = mb_strtolower($args[1]);
        }
    }

    // Grab path(s)
    self::verifyPaths($args[0]);

    return self::$files;
  }

  /**
   * Verifies the provided paths, checks if they exist as directories,
   * and categorizes them appropriately. If any paths do not exist,
   * the method outputs an error message and halts execution.
   *
   * @param string $paths A single directory path or an array of paths to be verified.
   * @return array|null Returns an array of directory contents if the paths are valid, otherwise halts execution.
   */
  private static function verifyPaths(string $paths)
  {
    $path_errors = [];

    if (gettype($paths) == 'string') {
      $paths = [$paths];
    }

    foreach ($paths as $path) {
      if (is_dir($path)) {
        self::$directories[] = $path;
        $dirContents = self::findContents($path);
      } else {
        $path_errors[] = $path;
      }
    }

    if ($path_errors) {
      print_r('The following directories do not exists<br />' . $path_errors, true);
      die();
    } else {
      return $dirContents;
    }
  }

  /**
   * Clears the working directory by removing all files and directories within it,
   * including a specific 'preloader.php' file if it exists.
   *
   * @return void
   */
  protected function clearWorkdir()
  {
    if (is_file($preload = implode(DIRECTORY_SEPARATOR, [static::$work_dir, 'preloader.php']))) {
      unlink($preload);
    }

    if (is_dir($this->workdir)) {
      foreach ((new Finder())->files()->in(static::$work_dir) as $file) {
        assert($file instanceof SplFileObject);
        unlink($file->getRealPath());
      }

      rmdir(implode(DIRECTORY_SEPARATOR, [static::$work_dir]));
    }
  }

  // This is how we scan directories

  /**
   * Recursively finds all files in a given directory and its subdirectories, filtered by extension if specified.
   *
   * @param string $dir The directory path to search within.
   * @return array An array containing the paths of all matched files.
   */
  private static function findContents(string $dir): array
  {
    $result = [];
    $root = scandir($dir);

    foreach ($root as $value) {
      if ($value === '.' || $value === '..') {
        continue;
      }

      if (is_file($dir . DIRECTORY_SEPARATOR . $value)) {
        if (!self::$ext_filter || in_array(mb_strtolower(pathinfo($dir . DIRECTORY_SEPARATOR . $value, PATHINFO_EXTENSION)), self::$ext_filter, true)) {
          self::$files[] = $result[] = $dir . DIRECTORY_SEPARATOR . $value;
        }
        continue;
      }

      if (self::$recursive) {
        foreach (self::findContents($dir . DIRECTORY_SEPARATOR . $value) as $new_value) {
          self::$files[] = $result[] = $new_value;
        }
      }
    }
    // Return required for recursive search
    return $result;
  }

  /**
   * Retrieves a list of PHP files from the Shop template directory using a recursive scan.
   *
   * @return array An array of file paths that match the specified criteria (PHP files, recursive search).
   */
  public static function getFiles(): array
  {
    $shop_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/template/';
    $result = static::scan($shop_directory, 'php', true);

    return $result;
  }
}