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

class HeaderOutputBootstrap
{
  /**
   * Generates and returns a string containing Bootstrap-related HTML meta tags and stylesheet links.
   * This ensures required Bootstrap assets are included for proper styling and icon usage.
   *
   * @return string The HTML markup for including Bootstrap stylesheet and icon assets.
   */
  public function display(): string
  {
//Note : Could be relation with a meta tag allowing to implement a new boostrap theme : Must be installed
    $output = '<!-- Start Bootstrap -->' . "\n";
    $output .= '<!-- CSS only -->';
    $output .= '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">' . "\n";
    $output .= '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">';
    $output .= '<link rel="stylesheet" href="' . CLICSHOPPING::link('css/bootstrap_icons_customize.css') . '" media="screen, print">';
    $output .= '<!-- Start Bootstrap -->' . "\n";

    return $output;
  }
}