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

  class HeaderOutputChartist
  {
    /**
     * @return bool|string
     */
    public function display(): string
    {
      $output = '<! -- Start Chartist -->' . "\n";
      $output .= '<link href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css" rel="stylesheet" type="text/css" />' . "\n";
      $output .= '<!-- End Chartist  -->' . "\n";

      return $output;
    }
  }