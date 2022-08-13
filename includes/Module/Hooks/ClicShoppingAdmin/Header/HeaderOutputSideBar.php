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

  class HeaderOutputSideBar
  {
    /**
     * @return string|bool
     */
    public function display(): string|bool
    {
      $output = '';

      if (isset($_SESSION['admin'])) {
        $output .= '<!--Sidebar Vertical Menu Script start-->' . "\n";
        $output .= '<link rel="stylesheet" href="' . CLICSHOPPING::link('css/headerOutputSideBar.css') . '" media="screen, print">' . "\n";
        $output .= '<!--End Sidebar Vertical Menu -->' . "\n";
      } else {
        return false;
      }

      return $output;
    }
  }