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

  use ClicShopping\OM\CLICSHOPPING;

  class FooterOutputBootstrapTable
  {
    /**
     * @return bool|string
     */
    public function display(): string
    {
      $output = '<! -- Start BootStrap Table -->' . "\n";
      $output .= '<script defer src="https://unpkg.com/bootstrap-table@1.15.5/dist/bootstrap-table.min.js"></script>' . "\n";
      $output .= '<script defer src="' . CLICSHOPPING::link('Shop/ext/javascript/bootstrapTable/table_checkbox.js') . '"></script>' . "\n";

      $output .= '<!-- End bootstrap Table -->' . "\n";

      return $output;
    }
  }