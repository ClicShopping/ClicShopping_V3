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

  namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Header;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTTP;
  
  class HeaderOutputBootstrap
  {
    /**
     * @return string
     */
    public function display(): string
    {
//Note : Could be relation with a meta tag allowing to implement a new boostrap theme : Must be installed
      $output = '<!-- Start Bootstrap -->' . "\n";
      $output .= '<!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">' . "\n";
      $output .= '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">';
      $output .= '<link rel="stylesheet" href="' . CLICSHOPPING::link('css/bootstrap_icons_customize.css')  . '" media="screen, print">';
      $output .= '<!-- Start Bootstrap -->' . "\n";
      
      return $output;
    }
  }