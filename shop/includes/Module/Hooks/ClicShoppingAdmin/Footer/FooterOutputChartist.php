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
      $output = '<! -- Start Chartist -->' . "\n";
      $output .= '<script defer src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js">' . "\n";
      $output .= '<!-- End Chartist  -->' . "\n";

      return $output;
    }
  }