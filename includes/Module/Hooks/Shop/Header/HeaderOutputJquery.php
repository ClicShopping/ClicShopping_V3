<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\Shop\Header;

class HeaderOutputJquery
{
  /**
   * Generates and returns a script tag string for including the jQuery library.
   *
   * @return string The HTML script tag string for the jQuery library.
   */
  public function display(): string
  {
    $output = '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>' . "\n";

    return $output;
  }
}