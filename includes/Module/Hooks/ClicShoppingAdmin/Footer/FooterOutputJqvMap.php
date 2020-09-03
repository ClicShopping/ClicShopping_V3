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

  class FooterOutputJqvMap
  {
    /**
     * @return string
     */
    public function display(): string
    {
      $params = $_SERVER['QUERY_STRING'];

      if (!empty($params)) {
        return false;
      }

      $output = '';

      if (isset($_SESSION['admin'])) {
        $output .= '<! -- Start Jqvmap -->' . "\n";
        $output .= '<script defer src="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jquery.vmap.min.js"></script>' . "\n";
        $output .= '<script defer src="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/maps/jquery.vmap.world.js"></script>' . "\n";
        $output .= '<!-- End Jqvmap  -->' . "\n";
      }

      return $output;
    }
  }