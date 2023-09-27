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
   * @return string
   */
  public function display(): string
  {
    $output = '<!-- Start Jquery -->' . "\n";
    $output .= '<script src="https://code.jquery.com/jquery-3.7.1.min.js"  integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>' . "\n";
    $output .= '<!-- Start Jquery -->' . "\n";

    return $output;
  }
}