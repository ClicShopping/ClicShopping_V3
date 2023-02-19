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
  use OpenAI\Exceptions\ErrorException;

  define('CLICSHOPPING_BASE_DIR', realpath(__DIR__ . '/../../includes/ClicShopping/') . '/');

  require_once(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');
  spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

  CLICSHOPPING::initialize();

  CLICSHOPPING::loadSite('ClicShoppingAdmin');

  $CLICSHOPPING_Db = Registry::get('Db');
  $CLICSHOPPING_Language = Registry::get('Language');

  $client = OpenAI::client(CLICSHOPPING_APP_CHATGPT_CH_API_KEY);

  $prompt = HTML::sanitize($_POST['message']);
  $engine = HTML::sanitize($_POST['engine']);

  $top = ['\n'];

  $parameters = [
    'model' => $engine,  // Spécification du modèle à utiliser
    'temperature' => (float)CLICSHOPPING_APP_CHATGPT_CH_TEMPERATURE, // Contrôle de la créativité du modèle
    'top_p' => 1, // Caractère de fin de ligne pour la réponse
    'frequency_penalty' => (float)CLICSHOPPING_APP_CHATGPT_CH_FREQUENCY_PENALITY,
    'presence_penalty' => 0,
    'prompt' => $prompt, // Texte d'amorce
    'max_tokens' => (int)CLICSHOPPING_APP_CHATGPT_CH_MAX_TOKEN,
    'stop' => $top,
    'n' => (int)CLICSHOPPING_APP_CHATGPT_CH_MAX_RESPONSE, // Nombre de réponses à générer
  ];

  $response = $client->completions()->create($parameters);

  try {
    $result = $response['choices'][0]['text'];

    if (isset($_POST['saveGpt'])) {
      $array_sql = [
        'question' => $prompt,
        'response' => $result,
        'date_added' => 'now()'
      ];

      $CLICSHOPPING_Db->save('chatgpt', $array_sql);
    }
  } catch (\RuntimeException $e) {
    throw new \Exception('Error appears, please look the console error');
  }


  echo $result;
