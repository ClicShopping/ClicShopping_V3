<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Header;

use ClicShopping\OM\CLICSHOPPING;

class HeaderOutputCodeMirror
{
  /**
   * @return string
   */
  public function display(): string
  {
    $output = '';

    if (isset($_SESSION['admin'])) {
      $output .= '<!-- Start Mirror -->' . "\n";
      $output .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css"/>' . "\n";
      $output .= '<link rel="stylesheet" href="' . CLICSHOPPING::link('css/codemirror.css') . '">' . "\n";
      $output .= '<!-- Start Code Mirror -->' . "\n";
    } else {
      return false;
    }

    return $output;
  }
}