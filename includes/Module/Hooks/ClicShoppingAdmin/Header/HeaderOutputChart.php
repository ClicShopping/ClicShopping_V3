<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Header;

  class HeaderOutputChart
  {
    /**
     * @return string
     */
    public function display(): string
    {
      $output = '';

      if (isset($_SESSION['admin'])) {
        $output = '<! -- Start Chart -->' . "\n";
        $output .= '<link href="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.css" rel="stylesheet" crossorigin="anonymous">';
        $output .= '<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>';
        $output .= '<!-- End Chart  -->' . "\n";
      }

      return $output;
    }
  }