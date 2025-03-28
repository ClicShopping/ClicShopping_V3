<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\ChatGpt\Classes\Shop\GptShop;

define('CLICSHOPPING_BASE_DIR', __DIR__ . '/../../../includes/ClicShopping/');

require_once(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');
spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

CLICSHOPPING::initialize();

CLICSHOPPING::loadSite('Shop');

$CLICSHOPPING_Db = Registry::get('Db');
$CLICSHOPPING_Language = Registry::get('Language');

if (GptShop::checkGptStatus() === false) {
  return false;
}

if (\defined('MODULES_FOOTER_CHATBOT_GPT_STATUS') && MODULES_FOOTER_CHATBOT_GPT_STATUS == 'False') {
  return false;
}

if (isset($_POST['message'])) {
  $question = HTML::sanitize($_POST['message']);
  $prompt = "You are an expert in marketing and e-commerce. Could you extract all the products name you can identified.  
    - Include only the products name identified and select the best product name you can find inside the result.
    - The products name are separated by a comma inside an arrax. There an array example ['word1', 'word2', 'word3'] about the result expected.
    - Just give the result without anything other information, just the array.
    - Remove the prompt, remove the product term, characteristics term and all other words.
    The request to analyse : " . $question;

  try {
    $result = GptShop::getGptResponse($prompt, $max_token = 20, $temperature = 0);
  } catch (Exception $e) {
    $result = 'Error';
  }

  $search_result = GptShop::productSearch($question, $result);

  echo nl2br($search_result);
}