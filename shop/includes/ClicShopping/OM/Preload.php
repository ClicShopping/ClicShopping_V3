<?php
  /**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *  @info : https://github.com/DarkGhostHunter/Preloader
 *
 */

  namespace ClicShopping\OM;

  use ClicShopping\OM\CLICSHOPPING;

  use DarkGhostHunter\Preloader\Preloader;

  class Preload
  {
    private static $work_dir = CLICSHOPPING::BASE_DIR . 'Work/Log/';
    private static $base_dir_autoload = CLICSHOPPING::BASE_DIR . 'External/vendor/autoload.php';
    private static $output_dir = CLICSHOPPING::BASE_DIR . 'Work/Log/preloader.php';
    private static $directories;
    private static $files;
    private static $ext_filter;
    private static $recursive;

    /**
     * @return bool
     */
    public static function check(): bool
    {
      $result = true;

      if (!is_writable(static::$work_dir)) $result = false;
      if (PHP_VERSION < 7.4) $result = false;
      if (CONFIGURATION_PRELOADING == 'false') $result = false;

      return $result;
    }

    /**
     * Execute Preload
     */
    public static function execute()
    {
      if (static::check() === true) {
        Preloader::make()
          ->autoload(static::$base_dir_autoload)
          ->output(static::$output_dir)
          ->memory(32)
          ->whenHits(200000)
          ->overwrite(true)
          ->append(
            static::getFiles()
          )
          ->generate();
      }
    }

    /**
     * Scan directory
     * @return array
     */
    public static function scan(): array
    {
      self::$recursive = false;
      self::$directories = [];
      self::$files = [];
      self::$ext_filter = false;

// Check we have minimum parameters
      if(!$args = func_get_args()){
        die('Must provide a path string or array of path strings');
      }

      if(gettype($args[0]) != 'string' && gettype($args[0]) != 'array'){
        die('Must provide a path string or array of path strings');
      }

      // Check if recursive scan | default action: no sub-directories
      if(isset($args[2]) && $args[2] === true) {
        self::$recursive = true;
      }

      // Was a filter on file extensions included? | default action: return all file types
      if(isset($args[1])){
        if(gettype($args[1]) == 'array'){
          self::$ext_filter = array_map('strtolower', $args[1]);
        }
        else
          if(gettype($args[1]) == 'string') {
            self::$ext_filter[] = strtolower($args[1]);
          }
      }

      // Grab path(s)
      self::verifyPaths($args[0]);

      return self::$files;
    }

    /**
     * @param $paths
     */
    private static function verifyPaths($paths)
    {
      $path_errors = [];

      if(gettype($paths) == "string"){$paths = array($paths);}

      foreach($paths as $path){
        if(is_dir($path)){
          self::$directories[] = $path;
          $dirContents = self::find_contents($path);
        } else {
          $path_errors[] = $path;
        }
      }

      if($path_errors){
        echo 'The following directories do not exists<br />';
        die();
      }
    }

    // This is how we scan directories

    /**
     * @param $dir
     * @return array
     */
    private static function find_contents(string $dir): array
    {
      $result = [];
      $root = scandir($dir);

      foreach($root as $value){
        if($value === '.' || $value === '..') {
          continue;
        }

        if(is_file($dir.DIRECTORY_SEPARATOR.$value)){
          if(!self::$ext_filter || in_array(strtolower(pathinfo($dir.DIRECTORY_SEPARATOR.$value, PATHINFO_EXTENSION)), self::$ext_filter)){
            self::$files[] = $result[] = $dir.DIRECTORY_SEPARATOR.$value;
          }
          continue;
        }

        if(self::$recursive){
          foreach (self::find_contents($dir.DIRECTORY_SEPARATOR.$value) as $value) {
            self::$files[] = $result[] = $value;
          }
        }
      }
      // Return required for recursive search
      return $result;
    }

    /**
     * @return array
     */
    public static function getFiles(): array
    {
      $shop_directory =  CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/template/';
      $shop_directory = static::scan($shop_directory, 'php', true);
      $result = $shop_directory;

      return $result;
    }

  }