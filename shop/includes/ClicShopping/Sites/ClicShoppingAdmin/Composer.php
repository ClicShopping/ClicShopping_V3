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

  namespace ClicShopping\Sites\ClicShoppingAdmin;

  use ClicShopping\OM\CLICSHOPPING;

  class Composer {
    protected static $root;
    protected static $composerJson;

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
     * Check if exec is enabled
     * @return bool
     */
    public static function checkExecEnabled(): bool
    {
      $disabled = explode(', ', ini_get('disable_functions'));
      return !in_array('exec', $disabled);
    }


    /**
     * check if composer is installed
     * @return bool
     */
    public static function checkComposerInstalled(): bool
    {
      $cmd = 'cd ' . self::$root . ' && composer show';
      exec($cmd, $output, $return); // update dependencies

      if ($return === 0) {
        $result = true;
      } else {
        return false;
      }

      return $result;
    }

    /**
     * check if a libray is installed
     * @return bool
     */
    public static function checkLibrayInstalled($libray = null): bool
    {
      if (!is_null($libray)) {
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
     * Check if exec or composer is authorise or installed
     * @return bool
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
     * To add inside a function with exit to see the result
     * @param $output
     * @param $return
     * @return string
     */
    public function debug($output, $return): string
    {
      $result = print_r($output,true) . ' - ' . $return;
      return $result;
    }

    /**
     * check if online version
     * @param null $library
     * @return string|null
     */
    public static function checkOnlineVersion($library = null)
    {
      if (self::checkExecute() === true) {
          if (!is_null($library)) {
          $cmd = 'cd ' . self::$root . ' && composer show ' . $library;
          exec($cmd, $output, $return);

          if($return === 0) {
            $result = $output[3];

            return $result;
          }
        } else {
          return false;
        }
      }
    }

    /**
     * List library installed
     * @return array
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
     * Update one or all library
     * @param null $library
     * @return string
     */
    public function update($library = null): string
    {
      if (self::checkExecute() === true) {
        if (is_null($library)) {
          $cmd = 'cd ' . self::$root . ' && composer update 2>&1';
          exec($cmd, $output, $return); // update dependencies

          $result = $output[2];
        } else {
          $cmd = 'cd ' . self::$root . ' && composer update  ' . $library . ' 2>&1';
          exec($cmd, $output, $return); // update dependencies

          $result = $output[2];
        }

        return $result;
      }
    }


    /**
     * Install a new library
     * @param null $library
     * @return bool|mixed
     */
    public static function install($library = null)
    {
      if (self::checkExecute() === true) {
        if (is_null($library)) {
          $result = false;
        } else {
          $cmd = 'cd ' . self::$root . ' && composer require  ' . $library . ' 2>&1';
          exec($cmd, $output, $return); // update dependencies

          $result = $output[2];
        }

        return $result;
      }
    }

    /**
     * remove ibrary
     * @param null $library
     * @return bool|mixed
     */
    public static function remove($library = null): string
    {
      if (self::checkExecute() === true) {
        $cmd = 'cd ' . self::$root . ' && composer remove ' . $library . ' 2>&1';
        exec($cmd, $output, $return); // update dependencies

        $result = $output[2];

        return $result;
      }
    }

    /**
     * Clear composer cache
     * @return string
     */
    public static function clearCache(): string
    {
      if (self::checkExecute() === true) {
        $cmd = 'cd ' . self::$root . ' && composer clearcache 2>&1';
        exec($cmd, $output, $return);

        $result = $output[2];

        return $result;
      }
    }
  }