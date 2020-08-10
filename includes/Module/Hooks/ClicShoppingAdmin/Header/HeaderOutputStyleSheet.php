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

  use ClicShopping\OM\CLICSHOPPING;

  class HeaderOutputStyleSheet
  {
    /**
     * @return bool|string
     */
    public function display(): string
    {
      $output = '<link rel="stylesheet preload" as="style" href="' . CLICSHOPPING::link('css/stylesheet.css') . '" media="screen, print">' . "\n";
      $output .= '<link rel="stylesheet preload" as="style" href="' . CLICSHOPPING::link('css/stylesheet_responsive.css') . '" media="screen, print">' . "\n";

      return $output;
    }
  }