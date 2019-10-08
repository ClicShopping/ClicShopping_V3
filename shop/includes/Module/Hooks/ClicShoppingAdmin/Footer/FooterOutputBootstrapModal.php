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

  class FooterOutputBootstrapModal
  {
    /**
     * @return string
     */
    public function display(): string
    {
      $output = '<!--Modal with remote url  Script start-->' . "\n";
      $output .= '<script src="' . CLICSHOPPING::link('Shop/ext/javascript/charcount/charCount.js') . '"></script>' . "\n";
      $output .= '<!--End Modal with remote url -->' . "\n";

      return $output;
    }
  }