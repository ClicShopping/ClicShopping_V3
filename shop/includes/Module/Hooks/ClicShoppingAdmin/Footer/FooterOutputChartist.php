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

  namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Footer;

  class FooterOutputChartist
  {
    /**
     * @return bool|string
     */
    public function display(): string
    {
      $version = '0.11.4';
      $output = '<! -- Start Chartist -->' . "\n";
      $output .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/chartist/' . $version . '/chartist.min.js"></script>' . "\n";
      $output .= '<!-- End Chartist  -->' . "\n";

      return $output;
    }
  }