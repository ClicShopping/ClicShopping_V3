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
  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

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

  if(isset($_POST['engine'])) {
    $engine = HTML::sanitize($_POST['engine']);
  } else {
    $engine = CLICSHOPPING_APP_CHATGPT_CH_MODEL;
  }

  $top = ['\n'];

  $parameters = [
    'model' => $engine,  // Spécification du modèle à utiliser
    'temperature' => (float)CLICSHOPPING_APP_CHATGPT_CH_TEMPERATURE, // Contrôle de la créativité du modèle
    'top_p' => (float)CLICSHOPPING_APP_CHATGPT_CH_TOP_P , // Caractère de fin de ligne pour la réponse
    'frequency_penalty' => (float)CLICSHOPPING_APP_CHATGPT_CH_FREQUENCY_PENALITY, //pénalité de fréquence pour encourager le modèle à générer des réponses plus variées
    'presence_penalty' => (float)CLICSHOPPING_APP_CHATGPT_CH_PRESENCE_PENALITY, //pénalité de présence pour encourager le modèle à générer des réponses avec des mots qui n'ont pas été utilisés dans l'amorce
    'prompt' => $prompt, // Texte d'amorce
    'max_tokens' => (int)CLICSHOPPING_APP_CHATGPT_CH_MAX_TOKEN, //nombre maximum de jetons à générer dans la réponse
    'stop' => $top, //caractères pour arrêter la réponse
    'n' => (int)CLICSHOPPING_APP_CHATGPT_CH_MAX_RESPONSE, // nombre de réponses à générer
    'best_of' => (int)CLICSHOPPING_APP_CHATGPT_CH_BESTOFF, //Generates best_of completions server-side and returns the "best"
  ];

  $response = $client->completions()->create($parameters);

  try {
    $result = $response['choices'][0]['text'];

    if (isset($_POST['saveGpt']) && $_POST['saveGpt'] == 1) {
      $array_sql = [
        'question' => $prompt,
        'response' => $result,
        'date_added' => 'now()',
        'user_admin' => AdministratorAdmin::getUserAdmin()
      ];

      $CLICSHOPPING_Db->save('chatgpt', $array_sql);
    }
  } catch (\RuntimeException $e) {
    throw new \Exception('Error appears, please look the console error');
  }

  echo $result;
