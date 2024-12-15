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

class FooterOutputLozad
{
  /**
   * Generates a Lazyload script block for administrators.
   *
   * @return string|bool Returns the generated output string if the session contains an 'admin' key, or false otherwise.
   */
  public function display(): string|bool
  {
    $output = '';

    if (isset($_SESSION['admin'])) {
      $output .= '<!--Lazyload Script start-->' . "\n";
      $output .= '<script src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"></script>' . "\n";
      $output .= '<script defer>';
      $output .= 'const observer = lozad(); observer.observe();';
      $output .= '</script>' . "\n";
      $output .= '<!--End Lazyload Script-->' . "\n";
    } else {
      return false;
    }

    return $output;
  }
}