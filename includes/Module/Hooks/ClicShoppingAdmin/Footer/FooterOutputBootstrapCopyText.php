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

  class FooterOutputBootstrapCopyText
  {
    /**
     * @return string|bool
     */
    public function display(): string|bool
    {
      $params = $_SERVER['QUERY_STRING'];

      if (empty($params)) {
        return false;
      }

      $output = '';

      if (isset($_SESSION['admin'])) {
        $output .= '<!--Copy text start-->' . "\n";
        $output .= '<script defer src="' . CLICSHOPPING::link('Shop/ext/javascript/clicshopping/ClicShoppingAdmin/tooltip_copy_text.js') . '"></script>' . "\n";
        $output .= '<!--Copy text end -->' . "\n";
      } else {
        return false;
      }

      return $output;
    }
  }