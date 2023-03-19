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

  use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Chat;

  class FooterOutputGpt
  {
    /**
     * @return bool|string
     */
    public function display(): string
    {
      $url = Chat::getAjaxUrl(true);

      $output = '<!-- Start gpt -->' . "\n";
      $output .= '<script defer>';
      $output .='document.addEventListener("DOMContentLoaded", function() {';
      $output .='document.querySelector("#sendGpt").addEventListener("click", function() {';
      $output .='let message = document.querySelector("#messageGpt").value;';
      $output .='let xhr = new XMLHttpRequest();';
      $output .='xhr.open("POST", "' . $url . '");';
      $output .='xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");';
      $output .='xhr.onreadystatechange = function() {';
      $output .='if (xhr.readyState === 4 && xhr.status === 200) {';
      $output .='document.querySelector("#chatGpt-output").innerHTML = xhr.responseText;';
      $output .='}';
      $output .='};';
      $output .='xhr.send("message=" + message);';
      $output .='});';
      $output .='});';
      $output .= '</script>';
      $output .= '<!-- End gpt  -->' . "\n";

      return $output;
    }
  }