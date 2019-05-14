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

  namespace ClicShopping\Sites\ClicShoppingAdmin;

  use ClicShopping\OM\CLICSHOPPING;

  class IndexAdmin {

    protected $size;
    protected $path;

/**
 * Return human readable sizes
 *
 * @param       int     $size        size in bytes
 * @param       string  $max         maximum unit
 * @param       string  $system      'si' for SI, 'bi' for binary prefixes
 * @param       string  $retstring   return string format
 */
    Public Static function getSizeReadable($size, $max = null, $system = 'si', $retstring = '%01.2f %s') {
      // Pick units
      $systems['si']['prefix'] = array('B', 'K', 'MB', 'GB', 'TB', 'PB');
      $systems['si']['size']   = 1000;
      $systems['bi']['prefix'] = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
      $systems['bi']['size']   = 1024;
      $sys = isset($systems[$system]) ? $systems[$system] : $systems['si'];

      // Max unit to display
      $depth = count($sys['prefix']) - 1;
      if ($max && false !== $d = array_search($max, $sys['prefix'])) {
        $depth = $d;
      }

      // Loop
      $i = 0;
      while ($size >= $sys['size'] && $i < $depth) {
        $size /= $sys['size'];
        $i++;
      }

      return sprintf($retstring, $size, $sys['prefix'][$i]);
    }


/**
 * Calculate the size of a directory by iterating its contents
 * @Access public
 * @Return size if the directory
 */
    Public Static function getDirSize() {

      $path = CLICSHOPPING::getConfig('dir_root', 'Shop');

      $dir = rtrim(str_replace('\\', '/', $path), '/');

      if (is_dir($dir) === true) {
        $totalSize = 0;
        $os = strtoupper(substr(PHP_OS, 0, 3));

// If on a Unix Host (Linux, Mac OS)
        if ($os !== 'WIN') {
          $io = popen('/usr/bin/du -sb ' . $dir, 'r');

          if ($io !== false) {
            $totalSize = intval(fgets($io, 80));
            pclose($io);

            return $totalSize;
          }
        }

// If on a Windows Host (WIN32, WINNT, Windows)
        if ($os === 'WIN' && extension_loaded('com_dotnet')) {
          $obj = new \COM('scripting.filesystemobject');

          if (is_object($obj)) {
            $ref       = $obj->getfolder($dir);
            $totalSize = $ref->size;
            $obj       = null;

            return $totalSize;
          }
        }

// If System calls did't work, use slower PHP 5
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));

        foreach ($files as $file) {
          $totalSize += $file->getSize();
        }

        return $totalSize;

      } elseif (is_file($dir) === true) {
        return filesize($dir);
      }
    }
  }