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

class FooterOutputSortable
{
  /**
   * Renders specific JavaScript code for a sortable functionality if certain conditions are met.
   *
   * @return string|bool Returns generated script code as a string if parameters and conditions are fulfilled; otherwise, returns false.
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
        $output .= '<script defer src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.6/Sortable.min.js"></script>' . "\n";
        $output .= '<!--Sortable end -->' . "\n";
      }
    } else {
      return false;
    }

    return $output;
  }
}