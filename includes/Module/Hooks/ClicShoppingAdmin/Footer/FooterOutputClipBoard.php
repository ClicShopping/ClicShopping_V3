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

class FooterOutputClipBoard
{
  /**
   * Generates and returns a string containing JavaScript code for using clipboard functionality.
   * The script is included only if the 'admin' session is set.
   *
   * @return string JavaScript code for clipboard functionality or an empty string if the session is not set.
   */
  public function display(): string
  {
    $output = '';

    if (isset($_SESSION['admin'])) {
      $output .= '<!-- Start Clipboard -->' . "\n";
      $output .= '<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>' . "\n";
      $output .= '<!-- End Clipboard  -->' . "\n";
    }

    return $output;
  }
}