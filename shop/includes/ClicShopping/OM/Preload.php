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
    private static string $work_dir = CLICSHOPPING::BASE_DIR . 'Work/Log/';
    private static string $base_dir_autoload = CLICSHOPPING::BASE_DIR . 'External/vendor/autoload.php';
    private static string $output_dir = CLICSHOPPING::BASE_DIR . 'Work/Log/preloader.php';
    private static array $files;
    private static bool $recursive;
    private static $ext_filter;
    private static array $directories;
    
    /**
     * @return bool
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
          ->useCompile()
          ->exclude([
              CLICSHOPPING::BASE_DIR . 'External/*'
          ])
          ->append(
            CLICSHOPPING::BASE_DIR . 'Apps/*',
            CLICSHOPPING::BASE_DIR . 'Sites/*',
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
    private static function verifyPaths(string $paths)
    {
      $path_errors = [];

      if(gettype($paths) == 'string'){
        $paths = [$paths];
      }

      foreach($paths as $path) {
        if(is_dir($path)) {
          self::$directories[] = $path;
          $dirContents = self::findContents($path);
        } else {
          $path_errors[] = $path;
        }
      }

      if($path_errors){
        print_r('The following directories do not exists<br />' . $path_errors, true);
        die();
      } else {
        return $dirContents;
      }
    }

    /**
     *
     */
    protected function clearWorkdir()
    {
      if (is_file($preload = implode(DIRECTORY_SEPARATOR, [static::$work_dir, 'preloader.php']))) {
        unlink($preload);
      }

      if (is_dir($this->workdir )) {
        foreach ((new Finder())->files()->in(static::$work_dir) as $file) {
          /** @var \SplFileObject $file */
          unlink($file->getRealPath());
        }

        rmdir(implode(DIRECTORY_SEPARATOR, [static::$work_dir]));
      }
    }

    // This is how we scan directories

    /**
     * @param $dir
     * @return array
     */
    private static function findContents(string $dir): array
    {
      $result = [];
      $root = scandir($dir);

      foreach($root as $value) {
        if($value === '.' || $value === '..') {
          continue;
        }

        if(is_file($dir.DIRECTORY_SEPARATOR.$value)){
          if(!self::$ext_filter || in_array(strtolower(pathinfo($dir . DIRECTORY_SEPARATOR . $value, PATHINFO_EXTENSION)), self::$ext_filter)){
            self::$files[] = $result[] = $dir . DIRECTORY_SEPARATOR . $value;
          }
          continue;
        }

        if(self::$recursive){
          foreach (self::findContents($dir . DIRECTORY_SEPARATOR . $value) as $new_value) {
            self::$files[] = $result[] = $new_value;
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
      $result = static::scan($shop_directory, 'php', true);

      return $result;
    }
  }