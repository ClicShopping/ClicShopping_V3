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

  class IndexAdmin
  {

    protected $size;
    protected $path;

    /**
     * Return human readable sizes
     *
     * @param int $size size in bytes
     * @param string $max maximum unit
     * @param string $system 'si' for SI, 'bi' for binary prefixes
     * @param string $retstring return string format
     */
    Public Static function getSizeReadable($size, $max = null, $system = 'si', $retstring = '%01.2f %s')
    {
      // Pick units
      $systems['si']['prefix'] = array('B', 'K', 'MB', 'GB', 'TB', 'PB');
      $systems['si']['size'] = 1000;
      $systems['bi']['prefix'] = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
      $systems['bi']['size'] = 1024;
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
    Public Static function getDirSize()
    {

      $path = CLICSHOPPING::getConfig('dir_root', 'Shop');
      $path = rtrim(str_replace('\\', '/', $path), '/');
      $bytestotal = 0;
      $path = realpath($path);

      if ($path !== false && $path != '' && file_exists($path)) {
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $object) {
          $bytestotal += $object->getSize();
        }
      }

      return $bytestotal;
    }
  }