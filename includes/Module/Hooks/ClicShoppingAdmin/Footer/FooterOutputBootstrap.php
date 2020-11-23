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

  class FooterOutputBootstrap
  {
    /**
     * @return bool|string
     */
    public function display(): string
    {

//Note : Could be relation with a meta tag allowing to implement a new boostrap theme : Must be installed
      $output = '<! -- Start BootStrap -->';

      $output .= '<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>' . "\n";
      $output .= '<!-- End bootstrap  -->' . "\n";

      return $output;
    }
  }