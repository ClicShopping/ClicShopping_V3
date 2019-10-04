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

  use ClicShopping\OM\Registry;

  class HeaderOutputBootstrap
  {
    public function display()
    {
      $CLICSHOPPING_Template = Registry::get('Template');

      $number = '4.3.1';

      $output = '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/' . $number . '/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">' . "\n";
      $output .= '<link rel="stylesheet" media="screen, print" href="' . $CLICSHOPPING_Template->getTemplategraphism() . '" />' . "\n";

     return $output;
    }
  }