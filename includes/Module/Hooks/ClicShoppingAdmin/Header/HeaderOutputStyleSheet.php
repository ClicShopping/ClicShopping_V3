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

use ClicShopping\OM\CLICSHOPPING;

class HeaderOutputStyleSheet
{
  /**
   * Generates and returns the HTML output for including the SmartMenus stylesheet links.
   *
   * @return string The generated HTML output containing the necessary stylesheet links for SmartMenus.
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