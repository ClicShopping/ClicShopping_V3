<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\Shop\Footer;

class FooterOutputBootStrap
{
  /**
   * Generates and returns a string containing the script for including the Bootstrap JavaScript bundle.
   *
   * @return string The HTML script tag for the Bootstrap JavaScript bundle.
   */
  public function display(): string
  {
    $output = '<!--Bootstrap Script start-->' . "\n";
    $output .= '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';
    $output .= '<!--End Bootstrap Script-->' . "\n";

    return $output;
  }
}