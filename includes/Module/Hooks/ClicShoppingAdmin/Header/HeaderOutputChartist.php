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
      $output = '';

      if (isset($_SESSION['admin'])) {
        $output = '<! -- Start Chartist -->' . "\n";
        $output .= '<link rel="stylesheet preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.4/chartist.min.css" media="screen, print"/>' . "\n";
        $output .= '<!-- End Chartist  -->' . "\n";
      }

      return $output;
    }
  }