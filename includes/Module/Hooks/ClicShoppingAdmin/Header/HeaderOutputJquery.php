<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
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
      $output = '<!-- Start Jquery -->' . "\n";
      $output .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>' . "\n";
      $output .= '<!-- Start Jquery -->' . "\n";

      return $output;
    }
  }