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

  class FooterOutputBootstrapTab
  {
    /**
     * @return string
     */
    public function display(): string
    {
      $output = '<!-- Bootstrap tab Script start-->' . "\n";
      $output .= '<script src="' . CLICSHOPPING::link('Shop/ext/javascript/bootstrap/tab/bootstrap_tab.js') . '"></script>' . "\n";
      $output .= '<!--Bootstrap tab end -->' . "\n";

      return $output;
    }
  }