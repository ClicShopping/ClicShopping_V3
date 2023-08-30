<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Footer;

class FooterOutputSortable
{
  /**
   * @return string|bool
   */
  public function display(): string|bool
  {
    $params = $_SERVER['QUERY_STRING'];

    if (empty($params)) {
      return false;
    }

    $output = '';

    if (isset($_SESSION['admin'])) {
      if (isset($_GET['cPath'])) {
        $output .= '<!-- Sortable Script start-->' . "\n";
        $output .= '<script defer src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>' . "\n";
        $output .= '<!--Sortable end -->' . "\n";
      }
    } else {
      return false;
    }

    return $output;
  }
}