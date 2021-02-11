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
  use ClicShopping\OM\HTTP;
  
  class HeaderOutputBootstrap
  {
    /**
     * @return bool|string
     */
    public function display()
    {
      $CLICSHOPPING_Template = Registry::get('Template');

//Note : Could be relation with a meta tag allowing to implement a new boostrap theme : Must be installed
      if (!\defined('MODULE_HEADER_TAGS_BOOTSTRAP_SELECT_THEME') || MODULE_HEADER_TAGS_BOOTSTRAP_SELECT_THEME == 'False') {
        $output = '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">' . "\n";
        $output .= '<link rel="stylesheet" media="screen, print" href="' . $CLICSHOPPING_Template->getTemplateCSS() . '" />' . "\n";
  
        $output .= '<link rel="stylesheet" href="' . HTTP::getShopUrlDomain() . 'ext/javascript/bootstrap/css/bootstrap_icons_1.2.1.css'  . '" media="screen, print">';
        return $output;
      } else {
        return false;
      }
    }
  }