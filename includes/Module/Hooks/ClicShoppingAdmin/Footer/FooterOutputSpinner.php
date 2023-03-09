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

  use ClicShopping\OM\CLICSHOPPING;

  class FooterOutputSpinner
  {
    /**
     * @return string|bool
     */
    public function display(): string|bool
    {
      $output = '';

      if (isset($_SESSION['admin']) && VERTICAL_MENU_CONFIGURATION == 'false') {
        $output .= '<!-- Start page loader spinner-->' . "\n";
        $output .= '<script defer src="' . CLICSHOPPING::link("Shop/ext/javascript/clicshopping/ClicShoppingAdmin/page_loader.js") . '"></script>' . "\n";
        $output .= '<!-- End page loader spinner -->' . "\n";
      } else {
        return false;
      }

      return $output;
    }
  }