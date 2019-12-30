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

  class HeaderOutputBootstrapTable
  {
    /**
     * @return bool|string
     */
    public function display(): string
    {
      $output = '<link rel="stylesheet preload" as="style" href="https://unpkg.com/bootstrap-table@1.15.5/dist/bootstrap-table.min.css">';

      return $output;
    }
  }