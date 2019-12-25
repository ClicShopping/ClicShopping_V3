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

  class HeaderOutputSmartMenus
  {
    /**
     * @return bool|string
     */
    public function display(): string
    {

      $output = '<link rel="stylesheet preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.smartmenus/1.0.1/css/sm-core-css.css" media="screen, print">' . "\n";
      $output .= '<link  rel="stylesheet preload" as="style" href="' . CLICSHOPPING::link('css/smartmenus.min.css') . '" media="screen, print">' . "\n";
      $output .= ' <link rel="stylesheet preload" as="style" href="' . CLICSHOPPING::link('css/smartmenus_customize.css') . '" media="screen, print">' . "\n";
      $output .= ' <link rel="stylesheet preload" as="style" href="' . CLICSHOPPING::link('css/smartmenus_customize_responsive.css') . '" media="screen, print">' . "\n";

      return $output;
    }
  }