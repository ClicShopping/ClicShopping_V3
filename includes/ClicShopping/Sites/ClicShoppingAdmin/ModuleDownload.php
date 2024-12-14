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
use ClicShopping\OM\FileSystem;
use ClicShopping\OM\HTML;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use function in_array;
use function is_array;
/**
 * Retrieves a list of all template directories and generates a dropdown menu using these directories.
 *
 * @param string $value The selected value for the dropdown menu.
 * @return string Returns the HTML dropdown menu based on the directory names.
 */
class ModuleDownload
{
  /**
   * Directory template with a drop down for all template
   *
   * @param string  all_template
   * @return string configuration_value, $filename_array,  $template_directory, the directory name
   *
   */

  public static function cfgPullDownAllTemplateDirectorylist($value)
  {

    $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/template/';
    $weeds = array('.', '..', '_notes', 'index.php', 'Default', 'ExNewTemplate');
    $directories = array_diff(scandir($template_directory), $weeds);
    $filename_array = [];

    if (is_array($directories)) {
      foreach ($directories as $value) {
        if (is_dir($template_directory . $value)) {
          $filename_array[] = ['id' => $value,
            'text' => $value
          ];
        }
      }
    }

    return HTML::selectMenu('configuration_value', $filename_array, $value);
  }

  /**
   * Copy file or folder from source to destination, it can do
   * recursive copy as well and is very smart
   * It recursively creates the dest file or directory path if there weren't exists
   * @param $source //file or folder
   * @param $dest ///file or folder
   * @param $options //folderPermission,filePermission
   * @return boolean
   */
  public static function smartCopy($source, $dest)
  {
    if (FileSystem::isWritable($dest, true)) {
      if (!is_dir($dest)) {
        @mkdir($dest, 0777, true);
      }
    }

    foreach (
      $iterator = new RecursiveIteratorIterator(
        new  RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
      ) as $item
    ) {
      if ($item->isDir()) {
        @mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
      } else {
        if ($item != $source . '/README.md' && $item != $source . '/LICENSE' && $item != 'ModuleInfosJson') {
          copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
        }
      }
    }
  }

  /*
   * Delete directory
   * param $dest string : folder path
   */
  public static function removeDirectory($source)
  {
    if (is_dir($source) === true) {
      $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::CHILD_FIRST);

      foreach ($files as $file) {
        if (in_array($file->getBasename(), array('.', '..')) !== true) {
          if ($file->isDir() === true) {
            rmdir($file->getPathName());
          } elseif (($file->isFile() === true) || ($file->isLink() === true)) {
            unlink($file->getPathname());
          }
        }
      }

      return rmdir($source);
    } elseif ((is_file($source) === true) || (is_link($source) === true)) {
      return unlink($source);
    }

    return false;
  }
}