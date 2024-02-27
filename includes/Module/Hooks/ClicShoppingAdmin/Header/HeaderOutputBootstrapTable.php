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

class HeaderOutputBootstrapTable
{
  /**
   * @return string
   */
  public function display(): string|bool
  {
    $output = '';

    if (isset($_SESSION['admin'])) {
      $output = '<!-- Start BootStrap Table -->' . "\n";
      $output .= '<link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.22.3/dist/bootstrap-table.min.css">' . "\n";
      $output .= '<!-- Start BootStrap Table -->' . "\n";
    } else {
      return false;
    }

    return $output;
  }
}