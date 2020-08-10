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

  class FooterOutputTokenInput
  {
    /**
     * @return string
     */
    public function display(): string
    {
      $output = '';

      if (isset($_SESSION['admin'])) {
        if (isset($_GET['cPath'])) {
          $output .= '<!--TokenInput Script start-->' . "\n";
          $output .= '<script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tokeninput/1.7.0/jquery.tokeninput.min.js"></script>' . "\n";
          $output .= '<!--End TokenInput-->' . "\n";
        }
      }

      return $output;
    }
  }