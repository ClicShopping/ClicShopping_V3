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

  class FooterOutputClipBoard
  {
    /**
     * @return bool|string
     */
    public function display(): string
    {
     $output = '';
     
      if (isset($_SESSION['admin'])) {
        $output .= '<!-- Start Clipboard -->' . "\n";
        $output .= '<script defer src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>';
        $output .= '<!-- End Clipboard  -->' . "\n";
      }
      
      return $output;
    }
  }