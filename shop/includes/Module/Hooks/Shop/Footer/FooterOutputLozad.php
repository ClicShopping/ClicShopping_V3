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

  class FooterOutputLozad
  {
    public function display()
    {
      $output = '<!--Lazyload Script start-->' . "\n";
      $output .= '<script>';
      $output .= 'const observer = lozad(); observer.observe();';
      $output .= '</script>' . "\n";
      $output .= '<script defer type="text/javascript" src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"></script>' . "\n";
      $output .= '<!--End Lazyload Script-->' . "\n";

      return $output;
    }
  }