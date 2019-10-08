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

  class HeaderOutputJquery
  {
    /**
     * @return string
     */
    public function display(): string
    {
     $output = '<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>' . "\n";

     return $output;
    }
  }