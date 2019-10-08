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

  class HeaderOutputGoogleFont
  {
    /**
     * @return bool|string
     */
    public function display(): string
    {
      $output = '<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>' . "\n";

      return $output;
    }
  }