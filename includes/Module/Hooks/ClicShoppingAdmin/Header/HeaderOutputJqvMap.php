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

  namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Header;

  use ClicShopping\OM\CLICSHOPPING;

  class HeaderOutputJqvMap
  {
    /**
     * @return string|bool
     */
    public function display(): string|bool
    {
      $params = $_SERVER['QUERY_STRING'];

      if (!empty($params)) {
        return false;
      }

      $output = '';

      if (isset($_SESSION['admin'])) {
        $output .= '<!-- Start Jqvmap -->' . "\n";
        $output .= '<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jqvmap.min.css" rel="stylesheet" media="screen" rel="preload"/>' . "\n";
        $output .= '<link type="text/css" href="' . CLICSHOPPING::link('css/jqvmap.css') . '" rel="stylesheet" rel="preload"/>' . "\n";
        $output .= '<!-- End Jqvmap  -->' . "\n";
      } else {
        return false;
      }

      return $output;
    }
  }