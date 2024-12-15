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

class FooterOutputMustache
{
  /**
   * Generates and returns a string containing Mustache.js script include tags
   * if the 'admin' session variable is set.
   * Returns false if the 'admin' session variable is not set.
   *
   * @return string|bool Returns the Mustache.js script tags as a string if 'admin' session variable exists, otherwise returns false.
   */
  public function display(): string|bool
  {
    $output = '';

    if (isset($_SESSION['admin'])) {
      $output .= '<!-- Mustache Script start-->' . "\n";
      $output .= '<script defer src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/4.1.0/mustache.min.js"></script>' . "\n";

      $output .= '<!--Mustache end -->' . "\n";
    } else {
      return false;
    }

    return $output;
  }
}