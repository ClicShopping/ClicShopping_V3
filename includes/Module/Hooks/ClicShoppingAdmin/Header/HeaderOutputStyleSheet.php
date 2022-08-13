<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Header;

  use ClicShopping\OM\CLICSHOPPING;

  class HeaderOutputStyleSheet
  {
    /**
     * @return string
     */
    public function display(): string
    {
      $output = '<!-- Start SmatMenus -->' . "\n";
      $output .= '<link rel="stylesheet" href="' . CLICSHOPPING::link('css/stylesheet.css') . '" media="screen, print">' . "\n";
      $output .= '<link rel="stylesheet" href="' . CLICSHOPPING::link('css/stylesheet_responsive.css') . '" media="screen, print">' . "\n";
      $output .= '<!-- Start SmatMenus -->' . "\n";
      
      return $output;
    }
  }