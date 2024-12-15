<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\ClicShoppingAdmin;

use ClicShopping\OM\CLICSHOPPING;
use function in_array;
use function is_null;
/**
 * The Composer class provides utility functions for managing Composer dependencies in a CLI/Shop context.
 * It supports checking the Composer environment, managing installed libraries, updating or installing new ones,
 * and clearing the Composer cache. Various utility methods ensure Composer and exec command functionality are available.
 */
class Composer
{
  protected static string $root;
  protected static string $composerJson;

  /**
   * Initializes the class by setting the root directory, composer.json file path,
   * and defining the composer environment variables.
   *
   * @return void
   */
  public function __construct()
  {
    static::$root = CLICSHOPPING::getConfig('dir_root', 'Shop');
    static::$composerJson = static::$root . 'composer.json';

//define  composer environment
    putenv('COMPOSER_HOME=' . self::$root);
    putenv('COMPOSER_CACHE_DIR=' . CLICSHOPPING::BASE_DIR . '/Work/Cache/Composer/');
    putenv('COMPOSER_HTACCESS_PROTECT=0');
  }

  /**
   * Checks if the 'exec' function is enabled on the server by inspecting the
   * 'disable_functions' directive from the PHP configuration.
   *
   * @return bool Returns true if the 'exec' function is enabled, false otherwise.
   */
  public static function checkExecEnabled(): bool
  {
    $disabled = explode(', ', ini_get('disable_functions'));

    return !in_array('exec', $disabled);
  }

  /**
   * Checks if Composer is installed and accessible in the current environment.
   *
   * This method verifies if the system has Composer installed by first checking
   * if the execution of shell commands is enabled. If execution is allowed,
   * it attempts to run `composer show` in the root directory. The method
   * returns true if the command executes successfully; otherwise, it returns false.
   *
   * @return bool Returns true if Composer is installed and accessible, false otherwise.
   */
  public static function checkComposerInstalled(): bool
  {
    if (self::checkExecEnabled() === true) {
      $cmd = 'cd ' . self::$root . ' && composer show';
      exec($cmd, $output, $return); // update dependencies

      if ($return === 0) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  /**
   * Checks if a specific library is installed using Composer.
   *
   * @param string|null $libray The name of the library to check. Defaults to null.
   * @return bool Returns true if the library is installed and command execution is enabled, otherwise false.
   */
  public static function checkLibrayInstalled($libray = null): bool
  {
    if (!is_null($libray) && self::checkExecute() === true) {
      $cmd = 'cd ' . self::$root . ' && composer show' . $libray;
      exec($cmd, $output, $return); // update dependencies

      if ($return === 0) {
        return false;
      } else {
        return true;
      }
    } else {
      return false;
    }
  }

  /**
   * Verifies if the necessary conditions for executing commands are met.
   *
   * This method checks whether Composer is installed and the `exec` function is enabled.
   *
   * @return bool Returns true if Composer is installed and the `exec` function is enabled, otherwise false.
   */
  private static function checkExecute(): bool
  {
    if (self::checkComposerInstalled() === false || self::checkExecEnabled() === false) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * Combines the provided output and return parameters into a formatted string.
   *
   * @param mixed $output The output to be included in the debug string.
   * @param string $return The return value to append to the debug string.
   * @return string The formatted debug string.
   */
  public function debug($output, $return): string
  {
    $result = print_r($output, true) . ' - ' . $return;
    return $result;
  }

  /**
   * Checks the online version of a specified library using Composer.
   *
   * @param string|null $library The name of the library whose version is to be checked. If null, the function will return false.
   * @return string|false The version information of the specified library if successful, or false if an error occurs or the library is not provided.
   */
  public static function checkOnlineVersion($library = null)
  {
    if (self::checkExecute() === true) {
      if (!is_null($library)) {
        $result = '';

        $cmd = 'cd ' . self::$root . ' && composer show ' . $library;
        exec($cmd, $output, $return);

        if ($return === 0) {
          if (isset($output)) {
            $result = $output[3];
          }

          return $result;
        }
      } else {
        return false;
      }
    }
  }

  /**
   * Retrieves the list of required libraries from the composer.json file.
   *
   * @return array An associative array of libraries and their version constraints, or an empty array if the file does not exist.
   */
  public static function getLibrary(): array
  {
    $composer_file = self::$composerJson;

    if (file_exists($composer_file)) {
      $composer_json = json_decode(file_get_contents($composer_file), true);

      return $composer_json['require'];
    } else {
      return [];
    }
  }

  /**
   * Updates all composer dependencies or a specific library if provided.
   *
   * @param string|null $library The name of the specific library to update. If null, updates all dependencies.
   * @return string The output message from the update operation, typically the third line of the composer output.
   */
  public static function update($library = null)
  {
    $result = '';

    if (self::checkExecute() === true) {
      if (is_null($library)) {
        $cmd = 'cd ' . self::$root . ' && composer update 2>&1';
        exec($cmd, $output, $return); // update dependencies

        $result = $output[2];
      } else {
        $cmd = 'cd ' . self::$root . ' && composer update  ' . $library . ' 2>&1';
        exec($cmd, $output, $return); // update dependencies

        if (isset($output)) {
          $result = $output[2];
        }
      }

      return $result;
    }
  }


  /**
   * Installs a specified library using Composer if execution is enabled.
   *
   * @param string|null $library The name of the library to install. If null, installation will not proceed.
   * @return string|bool Returns the output message of the installation process or false if the library parameter is null.
   */
  public static function install($library = null)
  {
    $result = '';

    if (self::checkExecute() === true) {
      if (is_null($library)) {
        $result = false;
      } else {
        $cmd = 'cd ' . self::$root . ' && composer require  ' . $library . ' 2>&1';
        exec($cmd, $output, $return); // update dependencies

        if (isset($output) && is_array($output)) {
          $result = $output[2];
        }
      }

      return $result;
    }
  }

  /**
   *
   * Removes a specified library using composer if execution of commands is enabled.
   *
   * @param string|null $library The name of the library to remove. Defaults to null.
   * @return string The result message from the remove operation.
   */
  public static function remove($library = null): string
  {
    if (self::checkExecute() === true) {
      $result = '';

      $cmd = 'cd ' . self::$root . ' && composer remove ' . $library . ' 2>&1';
      exec($cmd, $output, $return); // update dependencies

      if (isset($output)) {
        $result = $output[2];
      }

      return $result;
    }
  }

  /**
   * Clears the cache by executing the composer clearcache command.
   *
   * @return string The result of the cache clearing operation, typically a message or output from the command execution.
   */
  public static function clearCache(): string
  {
    if (self::checkExecute() === true) {
      $result = '';

      $cmd = 'cd ' . self::$root . ' && composer clearcache 2>&1';
      exec($cmd, $output, $return);

      if (isset($output)) {
        $result = $output[2];
      }

      return $result;
    }
  }
}