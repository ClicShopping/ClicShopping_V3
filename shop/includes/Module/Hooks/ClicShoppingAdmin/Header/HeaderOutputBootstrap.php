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

  class HeaderOutputBootstrap
  {
    /**
     * @return bool|string
     */
    public function display(): string
    {
      $number = '4.3.1';

//Note : Could be relation with a meta tag allowing to implement a new boostrap theme : Must be installed
      $output = '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/' . $number . '/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">' . "\n";

      return $output;
    }
  }