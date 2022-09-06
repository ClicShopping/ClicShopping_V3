<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\Registry;

  /**
   * Directory template with a drop down for all template
   *
   * @param string  all_template
   * @return string configuration_value, $filename_array,  $template_directory, the directory name
   *
   */

  function clic_cfg_set_all_template_directory_list_pull_down($value)
  {
    $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

    return $CLICSHOPPING_Template->getAllTemplate($value);
  }