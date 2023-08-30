<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\EditDesign\Classes;

use ClicShopping\OM\CLICSHOPPING;

class Gabari
{
  /**
   * Recursive Directory list file with all css under a drop down
   *
   * @return array c $filename_array, the file name in the  css subdirectory
   */
  public static function getFilenameGabari(): array
  {
    $filename_selected = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/template/' . SITE_THEMA . '/files/';

    $found = []; //initialize an array for matching files
    $fileTypes = ['php']; // Create an array of file types
    $found = []; // Traverse the folder, and add filename to $found array if type matches

    /* if empty error is produced : Fatal error: Uncaught exception 'RuntimeException'*/
    $file_array = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($filename_selected));

    foreach ($file_array as $filename => $current) {
      $fileInfo = pathinfo($current->getFileName());

      if (array_key_exists('extension', $fileInfo) && \in_array($fileInfo['extension'], $fileTypes)) {
        $found[] = $current->getFileName();
      }
    }

    if ($found) { // Check the $found array is not empty
      natcasesort($found); // Sort in natural, case-insensitive order, and populate menu

      $filename_array[0] = [
        'id' => '0',
        'text' => CLICSHOPPING::getDef('text_selected')
      ];

      foreach ($found as $filename) {
        $filename_array[] = [
          'id' => $filename,
          'text' => $filename
        ];
      }
    }
    return $filename_array;
  }

  /**
   *  Directory list
   *
   * @return array $directory_array, the directories name in css directory
   */
  public static function getDirectoryGabari(): array
  {
    $directory_array = [];
    $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/template/' . SITE_THEMA . '/files/';

    $exclude = ['.', '..', '_notes', 'index.php', '_htaccess', '.htaccess'];

    $directories = array_diff(scandir($template_directory), $exclude);

    $directory_array[0] = [
      'id' => '0',
      'text' => CLICSHOPPING::getDef('text_selected')
    ];

    foreach ($directories as $directory) {
      if (is_dir($template_directory . $directory)) {
        $directory_array[] = [
          'id' => $directory,
          'text' => $directory
        ];
      }
    }

    return $directory_array;
  }
}