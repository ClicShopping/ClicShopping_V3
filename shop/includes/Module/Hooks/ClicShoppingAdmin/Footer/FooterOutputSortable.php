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

  class FooterOutputSortable
  {
    /**
     * @return string
     */
    public function display(): string
    {
      $output = '<!-- Sortable Script start-->' . "\n";
      $output .= '<script defer src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.9.0/Sortable.min.js"></script>' . "\n";
      $output .= '<!--Sortable end -->' . "\n";

      return $output;
    }
  }