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
      $number = '4.3.1';

//Note : Could be relation with a meta tag allowing to implement a new boostrap theme : Must be installed
      $output = '<! -- Start BootStrap -->';
      $output .= '<script defer src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"  integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>' . "\n";
      $output .= '<script defer src="https://stackpath.bootstrapcdn.com/bootstrap/' . $number . '/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"  crossorigin="anonymous"></script>' . "\n";
      $output .= '<!-- End bootstrap  -->' . "\n";

      return $output;
    }
  }