<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\Apps\Configuration\ChatGpt\Classes\Shop\ChatGptShop35;

  define('CLICSHOPPING_BASE_DIR', __DIR__ . '/../../../includes/ClicShopping/');

  require_once(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');
  spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

  CLICSHOPPING::initialize();

  CLICSHOPPING::loadSite('Shop');

  $CLICSHOPPING_Db = Registry::get(('Db'));
  $CLICSHOPPING_Language = Registry::get(('Language'));

  if (!\defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') || CLICSHOPPING_APP_CHATGPT_CH_STATUS == 'False' || empty('CLICSHOPPING_APP_CHATGPT_CH_API_KEY')) {
      return false;
  }

  if (\defined('MODULES_FOOTER_CHATBOT_GPT_STATUS') && MODULES_FOOTER_CHATBOT_GPT_STATUS == 'False') {
    return false;
  }

  $question = HTML::sanitize($_POST['message']);
  $prompt = "Could you extract all the products name you can identified. 
  Remove the prompt, remove the product term, characteristics term and all other words.
  Include only the products name identified and select the best product name you can find inside the result.
  After that, explode every product name word inside an array. There an array example ['word1', 'word2', 'word3'] about the result expected.
  Just give the result without anything other information, just the array.
  The request to analyse : " . $question;

  $result = ChatGptShop35::getGptResponse($prompt, $max_token = 20, $temperature = 0);

  echo ChatGptShop35::productSearch($question, $result);