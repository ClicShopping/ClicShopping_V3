<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */


  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

/**
 * Directory list file with a drop down for blog
 *
 * @param string template_blog
 * @return string configuration_value., $filename_array,  $filename, hte file name in the directory
 * @access public
 */

  function clic_cfg_set_template_blog_summary_pull_down($filename, $key = '') {

    $module = $_GET['set'];

    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

    $template_directory = $CLICSHOPPING_Template->getDirectoryPathModuleShopTemplateHtml($module);

    if ($contents = @scandir($template_directory)) {
      $found = []; //initialize an array for matching files
      $fileTypes = array('php'); // Create an array of file types
      $found = []; // Traverse the folder, and add filename to $found array if type matches

      $name = ( ( $key ) ? 'configuration[' . $key . ']' : 'configuration_value' );

      foreach ($contents as $item) {
        $fileInfo = pathinfo($item);
        if(array_key_exists('extension', $fileInfo) && in_array($fileInfo['extension'],$fileTypes)) {
          $found[] = $item;
        }
      }

      if ($found) { // Check the $found array is not empty
        natcasesort($found); // Sort in natural, case-insensitive order, and populate menu
        $filename_array = [];
        foreach ($found as $filename){
          $filename_array[] = array('id' => $filename,
                                    'text' => $filename);
        }
      }
    }

    $QfileName = $CLICSHOPPING_Db->prepare('select configuration_value
                                      from :table_configuration
                                      where configuration_key = :configuration_key
                                     ');
    $QfileName->bindValue(':configuration_key', 'MODULE_BLOG_DISPLAY_SUMMARY_TEMPLATE');

    $QfileName->execute();
    $fileName_result = $QfileName->fetch();
    $filename  = $fileName_result['configuration_value'];

    return HTML::selectMenu($name, $filename_array,  $filename);
  }
