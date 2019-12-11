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

  namespace ClicShopping\OM\Module\Hooks\Shop\Header;

  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\HTML;

  class HeaderOutputManifest
  {
    /**
     * @return bool|string
     */
    public function display()
    {
      $output = '<link rel="manifest" href="' . HTTP::getShopUrlDomain() . 'manifest.php">' . "\n";
      $output .= '<link rel="apple-touch-icon" sizes="101x102" href="' . HTTP::getShopUrlDomain() . 'images/logo_clicshopping.png">' . "\n";

      $output .= '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
      $output .= '<meta name="apple-mobile-web-app-title" content="' . HTML::outputProtected(STORE_NAME) . '">' . "\n";
      $output .= '<meta name="theme-color" content="#317EFB"/>';

      return $output;
    }
  }