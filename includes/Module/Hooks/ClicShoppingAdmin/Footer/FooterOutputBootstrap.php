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

/**
 * Handles the inclusion of Bootstrap JavaScript resources in the admin footer of the ClicShopping platform.
 */
class FooterOutputBootstrap
{
  /**
   * Generates and returns a string containing the required HTML comments and script tags
   * to include the Bootstrap JavaScript library in a web page.
   *
   * @return string The generated HTML string with Bootstrap JavaScript inclusion.
   */
  public function display(): string
  {
    $output = '<!-- Start BootStrap -->' . "\n";
    $output .= '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';
    $output .= '<!-- End bootstrap  -->' . "\n";

    return $output;
  }
}