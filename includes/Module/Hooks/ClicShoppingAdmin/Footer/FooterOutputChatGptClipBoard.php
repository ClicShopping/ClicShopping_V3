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
   * Generates and returns HTML output for the admin clipboard functionality if the admin session is active and specific conditions are met.
   *
   * @return string The generated HTML output or an empty string if conditions are not met.
   */
  public function display(): string
  {
    $output = '';

    if (isset($_SESSION['admin'])) {
      $output = '<!-- Start Clipboard -->' . "\n";

      if (!defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') || CLICSHOPPING_APP_CHATGPT_CH_STATUS == 'True') {
        $url = CLICSHOPPING::getConfig('http_server', 'ClicShoppingAdmin') . CLICSHOPPING::getConfig('http_path', 'ClicShoppingAdmin') . 'ajax/chatGpt.php';

        $output .= '<script src="' . CLICSHOPPING::link('Shop/ext/javascript/clicshopping/ClicShoppingAdmin/chat_modal.js') . '"></script>';
        $output .= '<!-- End Clipboard -->' . "\n";
      }
    }

    return $output;
  }
}