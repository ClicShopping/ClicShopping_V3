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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
/**
 * Directory template with a drop down for all template
 *
 * @param string  all_template
 * @return string configuration_value, $filename_array,  $template_directory, the directory name
 * @access public
 */

  function  clic_cfg_set_all_template_directory_list_pull_down($value){

    $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . $CLICSHOPPING_Template->getTemplateDirectory() . '/';

    $weeds = array('.', '..', '_notes', 'index.php', 'ExNewTemplate', '.htaccess', 'README');

    $directories = array_diff(scandir($template_directory), $weeds);
    $filename_array = [];


    foreach($directories as $value) {
      if(is_dir($template_directory.$value)) {
        $filename_array[] = array('id' => $value,
                                  'text' => $value);
      }
    }

    return HTML::selectMenu($name, $filename_array, $value);
  }