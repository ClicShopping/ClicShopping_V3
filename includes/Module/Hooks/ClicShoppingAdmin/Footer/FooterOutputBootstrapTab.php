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

  class FooterOutputBootstrapTab
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
        $output .= '<!-- Bootstrap tab Script start-->' . "\n";
        $output .= '<script src="' . CLICSHOPPING::link('Shop/ext/javascript/bootstrap/tab/bootstrap_tab.js') . '"></script>' . "\n";
        $output .= '<!--Bootstrap tab end -->' . "\n";
      } else {
        return false;
      }

      return $output;
    }
  }