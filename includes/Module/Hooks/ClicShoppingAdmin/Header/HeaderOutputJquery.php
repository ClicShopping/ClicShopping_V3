<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Header;

class HeaderOutputJquery
{
  /**
   * Generates and returns a string containing HTML script tags for including the jQuery library.
   *
   * @return string The HTML string with the jQuery library script included.
   */
  public function display(): string
  {
    $output = '<!-- Start Jquery -->' . "\n";
    $output .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>' . "\n";
    $output .= '<!-- Start Jquery -->' . "\n";

    return $output;
  }
}