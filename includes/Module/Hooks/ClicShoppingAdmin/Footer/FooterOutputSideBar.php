<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Footer;

class FooterOutputSideBar
{
  /**
   * @return string|bool
   */
  public function display(): string|bool
  {
    $output = '';

    if (isset($_SESSION['admin']) && VERTICAL_MENU_CONFIGURATION == 'true') {
      $output .= '<!--Sidebar Vertical Menu Script start-->' . "\n";
      $output .= '<script defer>' . "\n";
      $output .= '$(function() {  $(\'#sidebarCollapse\').on(\'click\', function() { $(\'#sidebar, #content\').toggleClass(\'active\');  }); });' . "\n";
      $output .= '$(function() {  $(\'#sidebarCollapse1\').on(\'click\', function() { $(\'#sidebar, #content\').toggleClass(\'active\');  }); });' . "\n";
      $output .= '</script>' . "\n";
      $output .= '<!--End Sidebar Vertical Menu -->' . "\n";
    } else {
      return false;
    }

    return $output;
  }
}