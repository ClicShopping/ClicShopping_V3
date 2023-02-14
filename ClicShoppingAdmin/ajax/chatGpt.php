<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  use OpenAI;

  define('CLICSHOPPING_BASE_DIR', realpath(__DIR__ . '/../../includes/ClicShopping/') . '/');

  require_once(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');
  spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

  CLICSHOPPING::initialize();

  CLICSHOPPING::loadSite('ClicShoppingAdmin');

  $CLICSHOPPING_Db = Registry::get('Db');
  $CLICSHOPPING_Language = Registry::get('Language');

  $client = OpenAI::client(CLICSHOPPING_APP_CHATGPT_CH_API_KEY);

  $prompt = HTML::sanitize($_POST['message']);

  $array = [
    'model' => 'text-davinci-003',
    'temperature' => 0.9,
    'top_p' => 1,
    'frequency_penalty' => 0,
    'presence_penalty' => 0,
    'prompt' => $prompt,
    'max_tokens' => 4000,
  ];

  $response = $client->completions()->create($array);

  $result = $response['choices'][0]['text'];

  $array = [
    'question' => $prompt,
    'response' => $result,
    'date_added' => 'now()'
  ];

    $CLICSHOPPING_Db->save('chatgpt', $array);

  echo $result;
