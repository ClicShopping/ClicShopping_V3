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

class HeaderOutputChart
{
  /**
   * @return string|bool
   */
  public function display(): string|bool
  {
    $output = '';

    if (isset($_SESSION['admin'])) {
      $output = '<!-- Start Chart -->' . "\n";
      $output .= '<link href="https://cdn.jsdelivr.net/npm/chart.css" rel="stylesheet" crossorigin="anonymous">' . "\n";
      $output .= '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0"></script>' . "\n";
      $output .= '<!-- End Chart -->' . "\n";
    } else {
      return false;
    }

    return $output;
  }
}