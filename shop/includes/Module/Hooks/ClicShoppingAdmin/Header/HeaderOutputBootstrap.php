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

  class HeaderOutputBootstrap
  {
    /**
     * @return bool|string
     */
    public function display(): string
    {
//Note : Could be relation with a meta tag allowing to implement a new boostrap theme : Must be installed
      $output = '<link rel="stylesheet preload" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.css" integrity="sha384-vXOtxoYb1ilJXRLDg4YD1Kf7+ZDOiiAeUwiH9Ds8hM8Paget1UpGPc/KlaO33/nt" crossorigin="anonymous">' . "\n";

      return $output;
    }
  }