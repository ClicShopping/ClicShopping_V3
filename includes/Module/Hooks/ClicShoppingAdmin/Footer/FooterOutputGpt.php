<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Footer;

use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Gpt;

use function defined;

class FooterOutputGpt
{
  /**
   * @return bool|string
   */
  public function display(): string
  {
    $output = '';

    if (isset($_SESSION['admin'])) {
      if (!defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') || CLICSHOPPING_APP_CHATGPT_CH_STATUS == 'True') {

        $url = Gpt::getAjaxUrl(true);

        $output .= '<!-- Start gpt modal header-->' . "\n";
        $output .= '<script defer>';
        $output .= 'document.addEventListener("DOMContentLoaded", function() {';
        $output .= '  var sendGptButton = document.querySelector("#sendGpt");';
        $output .= '  if (sendGptButton) {';
        $output .= '    sendGptButton.addEventListener("click", function() {';
        $output .= '      let message = document.querySelector("#messageGpt").value;';
        $output .= '      let saveGptElement = document.querySelector("#saveGpt");';
        $output .= '      let saveGpt = saveGptElement ? (saveGptElement.checked ? 1 : 0) : 0;';
        $output .= '      fetch("' . $url . '", {';
        $output .= '        method: "POST",';
        $output .= '        headers: {';
        $output .= '          "Content-Type": "application/x-www-form-urlencoded",';
        $output .= '        },';
        $output .= '        body: "message=" + encodeURIComponent(message) + "&saveGpt=" + saveGpt,';
        $output .= '      })';
        $output .= '      .then(response => response.text())';
        $output .= '      .then(data => {';
        $output .= '        document.querySelector("#chatGpt-output").innerHTML = data;';
        $output .= '      })';
        $output .= '      .catch(error => console.error("Erreur lors de la requête fetch:", error));';
        $output .= '    });';
        $output .= '  }';
        $output .= '});';
        $output .= '</script>' . "\n";

        $output .= '<!-- End gpt  -->' . "\n";
      }
    }

    return $output;
  }
}