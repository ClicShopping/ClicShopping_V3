<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Footer;

  class FooterOutputLozad
  {
    /**
     * @return string|bool
     */
    public function display(): string|bool
    {
      $output = '';

      if (isset($_SESSION['admin'])) {
        $output .= '<!--Lazyload Script start-->' . "\n";
        $output .= '<script src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"></script>' . "\n";
        $output .= '<script defer>';
        $output .= 'const observer = lozad(); observer.observe();';
        $output .= '</script>' . "\n";
        $output .= '<!--End Lazyload Script-->' . "\n";
      } else {
        return false;
      }

      return $output;
    }
  }