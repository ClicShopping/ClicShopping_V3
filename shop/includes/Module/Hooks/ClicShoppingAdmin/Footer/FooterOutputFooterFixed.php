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

  class FooterOutputFooterFixed
  {
    /**
     * @return string
     */
    public function display(): string
    {
      $output = '<!-- Footer Script start-->' . "\n";
      $output .= '<script src="' . CLICSHOPPING::link('Shop/ext/javascript/clicshopping/ClicShoppingAdmin/footer.js') . '"></script>' . "\n";
      $output .= '<!--Footer end -->' . "\n";

      return $output;
    }
  }