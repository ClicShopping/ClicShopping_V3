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
 * @param       string   $directory    Path to directory
 */
    Public Static function getDirSize($path) {
      // Init
      $size = 0;

      // Trailing slash
      if (substr($path, -1, 1) !== DIRECTORY_SEPARATOR) {
        $path .= DIRECTORY_SEPARATOR;
      }
// path
      $path = CLICSHOPPING::getConfig('dir_root', 'Shop');
      // Sanity check
      if (is_file($path)) {
        return filesize($path);
      } elseif (!is_dir($path)) {
        return false;
      }

      // Iterate queue
      $queue = array($path);

      for ($i = 0, $j = count($queue); $i < $j; ++$i) {
        // Open directory
        $parent = $i;
        if (is_dir($queue[$i]) * $dir = @dir($queue[$i])) {
          $subdirs = [];
          while (false !== ($entry = $dir->read())) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
              continue;
            }

            // Get list of directories or filesizes
            $path = $queue[$i] . $entry;
            if (is_dir($path)) {
              $path .= DIRECTORY_SEPARATOR;
              $subdirs[] = $path;
            } elseif (is_file($path)) {
              $size += filesize($path);
            }
          }

          // Add subdirectories to start of queue
          unset($queue[0]);
          $queue = array_merge($subdirs, $queue);

          // Recalculate stack size
          $i = -1;
          $j = count($queue);

          // Clean up
          $dir->close();
          unset($dir);
        }
      }
      return $size;
    }
  }