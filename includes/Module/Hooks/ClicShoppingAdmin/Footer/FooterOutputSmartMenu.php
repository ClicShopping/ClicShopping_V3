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

use ClicShopping\OM\CLICSHOPPING;

class FooterOutputSmartMenu
{
  /**
   * Generates and returns the HTML output for including SmartMenu scripts if certain conditions are met.
   *
   * @return string|bool The HTML output as a string if conditions are met; false otherwise.
   */
  public function display(): string|bool
  {
    $output = '';

    if (isset($_SESSION['admin']) && VERTICAL_MENU_CONFIGURATION == 'false') {
      $output .= '<!--SmartMenu Script start-->' . "\n";
      $output .= '<script defer src="' . CLICSHOPPING::link('Shop/ext/javascript/clicshopping/ClicShoppingAdmin/smartmenus_config.js') . '"></script>' . "\n";
      $output .= '<script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery.smartmenus/1.2.1/jquery.smartmenus.min.js"></script>' . "\n";
      $output .= '<!--End SmartMenu-->' . "\n";
    } else {
      return false;
    }

    return $output;
  }
}