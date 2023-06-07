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

  class Listing
  {
    /*
      * Recursive Directory list file with all template products under a drop down
      * @param string $filename : name of the file
      * @return array $filename_array, the file name in the template products subdirectory
      *
    */
    public static function getFilenameTemplateProducts(): ?array
    {
      if (isset($_POST['directory_html'])) {
        $directory_selected = HTML::sanitize($_POST['directory_html']) . '/';
      } else {
        $directory_selected = HTML::sanitize(['directory_html']) . '/';
      }

      if (file_exists(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/template/' . SITE_THEMA . '/modules/' . $directory_selected . '/template_html/')) {
        $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/template/' . SITE_THEMA . '/modules/' . $directory_selected . '/template_html/';
      } else {
        $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/template/' . SITE_THEMA . '/modules/';
      }

      $found = []; //initialize an array for matching files
      $fileTypes = ['php']; // Create an array of file types
      $found = []; // Traverse the folder, and add filename to $found array if type matches
      $filename_array = [];

      /* if empty error is produced : Fatal error: Uncaught exception 'RuntimeException'*/
      $file_array = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($template_directory));

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

    /*
      * Template_products Directory list
      * @param string $filename : name of the file
      * @return string $directory_array, the directories name in css directory
      *
    */
    public static function getDirectoryTemplateProducts(): array
    {
      $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/template/' . SITE_THEMA . '/modules/';

      $exclude = [
        '..',
        '.',
        'customers_address',
        'download',
        'index.php',
        '_htaccess',
        '.htaccess',
        'modules_account_customers',
        'modules_advanced_search',
        'modules_blog_content',
        'modules_boxes',
        'modules_checkout_confirmation',
        'modules_checkout_payment',
        'modules_checkout_shipping',
        'modules_checkout_success',
        'modules_contact_us',
        'modules_create_account',
        'modules_create_account_pro',
        'modules_footer_suffix',
        'modules_login',
        'modules_products_reviews',
        'modules_shopping_cart',
        'modules_sitemap',
        'modules_tell_a_friend',
      ];

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
