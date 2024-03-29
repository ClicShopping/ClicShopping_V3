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

use ClicShopping\OM\CLICSHOPPING;
use function defined;

class FooterOutputChatGptClipBoard
{
  /**
   * @return bool|string
   */
  public function display(): string
  {
    $output = '';

    if (isset($_SESSION['admin'])) {
      $output = '<!-- Start Clipboard -->' . "\n";

      if (!defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') || CLICSHOPPING_APP_CHATGPT_CH_STATUS == 'True') {
        $url = CLICSHOPPING::getConfig('http_server', 'ClicShoppingAdmin') . CLICSHOPPING::getConfig('http_path', 'ClicShoppingAdmin') . 'ajax/chatGpt.php';

        $output .= '<script defer>';
        $output .= '
         $(document).ready(function() {
          // Initialize the clipboard for result button
          var clipboardResult = new ClipboardJS("#copyResultButton");
            
          // Handler for when the result button is clicked
          clipboardResult.on("success", function(e) {
            // Show a tooltip indicating that the text was copied
            $(e.trigger).tooltip({title: "Copied!", placement: "bottom", trigger: "manual"}).tooltip("show");
            setTimeout(function() {
              $(e.trigger).tooltip("hide");
            }, 1000);
            e.clearSelection();
          });
              
          // Initialize the clipboard for HTML button
          var clipboardHTML = new ClipboardJS("#copyHTMLButton", {
            target: function() {
              return document.querySelector("#chatGpt-output");
            }
          });
        
          // Handler for when the HTML button is clicked
          clipboardHTML.on("success", function(e) {
            // Show a tooltip indicating that the HTML was copied
            $(e.trigger).tooltip({title: "Copied HTML!", placement: "bottom", trigger: "manual"}).tooltip("show");
            setTimeout(function() {
              $(e.trigger).tooltip("hide");
            }, 1000);
            e.clearSelection();
          });
        
          $("#sendGpt").click(function() {
            let message = $("#messageGpt").val();
            let engine = $("#engine").val();
            let saveGptElement = document.querySelector("#saveGpt");
            let saveGpt = saveGptElement ? (saveGptElement.checked ? 1 : 0) : 0;
                       
            let data = {
                message: message,
                engine: engine,
                saveGpt: saveGpt,
            };
            
            $.post("' . $url . '", data, function(data) {
              $("#chatGpt-output").html(data);
              // Show the copy buttons after the chat message is generated
              $("#copyResultButton, #copyHTMLButton").removeClass("d-none");
            });
          });
        });
      ';

        $output .= '</script>';

        $output .= '<!-- End Clipboard -->' . "\n";
      }
    }

    return $output;
  }
}