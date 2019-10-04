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

  namespace ClicShopping\OM\Module\Hooks\Shop\Footer;

  class FooterOutputFontAwesome
  {
    public function display()
    {
      $output = '<!--FontAwesome Script start-->' . "\n";
      $output .= '<script defer src="https://kit.fontawesome.com/89fdf54890.js"></script>' . "\n";
      $output .= '<!--End FontAwesomeScript-->' . "\n";

      return $output;
    }
  }