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

  class FooterOutputCharCount
  {
    /**
     * @return string
     */
    public function display(): string
    {
      $output = '<!--count words  Script start-->' . "\n";
      $output .= '<script defer src="' . CLICSHOPPING::link('Shop/ext/javascript/charcount/charCount.min.js') . '"></script>' . "\n";
      $output .= '<!--End count words -->' . "\n";

      return $output;
    }
  }