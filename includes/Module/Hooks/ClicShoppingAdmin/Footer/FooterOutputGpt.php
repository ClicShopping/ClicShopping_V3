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

  class FooterOutputGpt
  {
    /**
     * @return bool|string
     */
    public function display(): string
    {
      $output = '<!-- Start BootStrap -->' . "\n";

      $url = CLICSHOPPING::getConfig('http_server', 'ClicShoppingAdmin') . CLICSHOPPING::getConfig('http_path', 'ClicShoppingAdmin') . 'ajax/chatGpt.php';

      $output .= '<script defer>';
      $output .= '$(document).ready(function() {';
      $output .= '$("#sendGpt").click(function() {';
      $output .= 'let message = $("#messageGpt").val();';
      $output .= '$.post("' . $url . '", {message: message}, function(data) {';
      $output .= '$("#chatGpt-output").html(data);';
      $output .= '});';
      $output .= '});';
      $output .= '});';
      $output .= '</script>';

      $output .= '<!-- End bootstrap  -->' . "\n";

      return $output;
    }
  }