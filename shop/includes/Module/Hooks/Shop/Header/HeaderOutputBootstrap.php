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

  namespace ClicShopping\OM\Module\Hooks\Shop\Header;

  use ClicShopping\OM\Registry;

  class HeaderOutputBootstrap
  {
    /**
     * @return bool|string
     */
    public function display()
    {
      $CLICSHOPPING_Template = Registry::get('Template');

      $number = '4.4.1';

//Note : Could be relation with a meta tag allowing to implement a new boostrap theme : Must be installed
      if (!defined('MODULE_HEADER_TAGS_BOOTSTRAP_SELECT_THEME') || MODULE_HEADER_TAGS_BOOTSTRAP_SELECT_THEME == 'False') {

        $output = '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/' . $number .'/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">' . "\n";
        $output .= '<link rel="stylesheet" media="screen, print" href="' . $CLICSHOPPING_Template->getTemplategraphism() . '" />' . "\n";

        return $output;
      } else {
        return false;
      }
    }
  }