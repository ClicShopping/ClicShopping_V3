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
  use ClicShopping\OM\HTML;

  class Module
  {
    /**
     * Recursive Directory list file with all css under a drop down
     *
     * @return array $filename_array, the file name in the  css subdirectory
     * @access public
     */

    public static function getFilenameHtml() :array
    {
      if (isset($_POST['directory_html'])) {
        $directory_selected = HTML::sanitize($_POST['directory_html']) . '/';
      } else {
        $directory_selected = HTML::sanitize($_GET['directory_html']) . '/';
      }

      if (file_exists(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/template/' . SITE_THEMA . '/modules/' . $directory_selected . 'content/')) {
        $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/template/' . SITE_THEMA . '/modules/' . $directory_selected . 'content/';
      } else {
        $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/template/' . SITE_THEMA . '/modules/';
      }

      $found = []; //initialize an array for matching files
      $fileTypes = ['php']; // Create an array of file types
      $found = []; // Traverse the folder, and add filename to $found array if type matches

      /* if empty error is produced : Fatal error: Uncaught exception 'RuntimeException'*/
      foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($template_directory)) as $filename => $current) {
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
     * HTML Directory list
     *
     * @return array $directory_array, the directories name in css directory
     * @access public
     */

    public static function getDirectoryHtml() :array
    {
      $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/template/' . SITE_THEMA . '/modules/';

      $exclude = ['.', '..', '_notes', 'customers_address', 'download', 'index.php', '_htaccess', '.htaccess'];
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
