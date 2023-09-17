<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use GuzzleHttp\Client as GuzzleHttpClient;
use OpenAI;
use RuntimeException;

use function defined;
use function is_null;

class ChatGptAdmin35
{
  /**
   * @return bool
   */
  public static function checkGptStatus(): bool
  {
    if (!defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') || CLICSHOPPING_APP_CHATGPT_CH_STATUS == 'False' || empty('CLICSHOPPING_APP_CHATGPT_CH_API_KEY')) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * @param bool $chatGpt
   * @return string
   */
  public static function getAjaxUrl(bool $chatGpt = true): string
  {
    if ($chatGpt === false) {
      $url = CLICSHOPPING::getConfig('http_server', 'ClicShoppingAdmin') . CLICSHOPPING::getConfig('http_path', 'ClicShoppingAdmin') . 'ajax/chatGptSEO.php';
    } else {
      $url = CLICSHOPPING::getConfig('http_server', 'ClicShoppingAdmin') . CLICSHOPPING::getConfig('http_path', 'ClicShoppingAdmin') . 'ajax/chatGpt.php';
    }

    return $url;
  }

  /**
   * @return string
   */
  public static function getAjaxSeoMultilanguageUrl(): string
  {
    $url = CLICSHOPPING::getConfig('http_server', 'ClicShoppingAdmin') . CLICSHOPPING::getConfig('http_path', 'ClicShoppingAdmin') . 'ajax/chatGptMultiLanguage.php';

    return $url;
  }

  /**
   * @return array
   */
  private static function getGptModel(): array
  {
    $array = [
      ['id' => 'gpt-3.5-turbo',
        'text' => 'gpt-3.5-turbo'
      ],
    ];

    return $array;
  }

  /**
   * @return OpenAI\Client
   */
  private static function getClient()
  {
    if (!empty(CLICSHOPPING_APP_CHATGPT_CH_API_KEY)) {

      $client = OpenAI::factory()
      ->withApiKey(CLICSHOPPING_APP_CHATGPT_CH_API_KEY)
      ->withBaseUri('api.openai.com/v1') // default: api.openai.com/v1
      ->withHttpClient($client = new GuzzleHttpClient()) // default: HTTP client found using PSR-18 HTTP Client Discovery
      ->withHttpHeader('X-My-Header', 'ClicShopping')
      ->withStreamHandler(fn(RequestInterface $request): ResponseInterface => $client->send($request, [
        'stream' => true // Allows providing a custom stream handler for the http client.
      ]))
      ->make();

    return $client;
    } else {
      return false;
    }
  }

  /**
   * @param string $question
   * @param int|null $maxtoken
   * @param float|null $temperature
   * @throws \Exception
   */
  public static function getGptResponse(string $question, ?int $maxtoken = null, ?float $temperature = null): bool|string
  {
    if (self::checkGptStatus() === false) {
      return false;
    }

    $client = self::getClient();

    $prompt = HTML::sanitize($question);

    $modelArray = self::getGptModel();
    $modelId = $modelArray[0]['id'];
    $engine = $modelId;

    $top = ['\n'];

    if (is_null($maxtoken)) {
      $maxtoken = (int)CLICSHOPPING_APP_CHATGPT_CH_MAX_TOKEN;
    }

    if (is_null($temperature)) {
      $temperature = (float)CLICSHOPPING_APP_CHATGPT_CH_TEMPERATURE;
    }

    $parameters = [
      'model' => $engine,  // Spécification du modèle à utiliser
      'temperature' => $temperature, // Contrôle de la créativité du modèle
      'top_p' => (float)CLICSHOPPING_APP_CHATGPT_CH_TOP_P, // Caractère de fin de ligne pour la réponse
      'frequency_penalty' => (float)CLICSHOPPING_APP_CHATGPT_CH_FREQUENCY_PENALITY, //pénalité de fréquence pour encourager le modèle à générer des réponses plus variées
      'presence_penalty' => (float)CLICSHOPPING_APP_CHATGPT_CH_PRESENCE_PENALITY, //pénalité de présence pour encourager le modèle à générer des réponses avec des mots qui n'ont pas été utilisés dans l'amorce
      'max_tokens' => $maxtoken, //nombre maximum de jetons à générer dans la réponse
      'stop' => $top, //caractères pour arrêter la réponse
      'n' => (int)CLICSHOPPING_APP_CHATGPT_CH_MAX_RESPONSE, // nombre de réponses à générer
      'messages' => [
        [
          'role' => 'system',
          'content' => 'You are the customer service.'
        ],
        [
          'role' => 'user',
          'content' => $prompt
        ]
      ],
    ];

    if (!empty(CLICSHOPPING_APP_CHATGPT_CH_ORGANIZATION)) {
      $parameters = [
        'organization' => CLICSHOPPING_APP_CHATGPT_CH_ORGANISATION,
      ];
    }

    $response = $client->chat()->create($parameters);

    try {
      $result = $response['choices'][0]['message']['content'];

      $array_usage = [
        'promptTokens' => $response->usage->promptTokens,
        'completionTokens' => $response->usage->completionTokens,
        'totalTokens' => $response->usage->totalTokens,
      ];

      static::saveData($question, $result, $array_usage);

      return $result;
    } catch (RuntimeException $e) {
      throw new \Exception('Error appears, please look the console error');

      return false;
    }
  }

  /**
   * @param string $question
   * @param string $result
   * @param array $usage
   */
  private static function saveData(string $question, string $result, array $usage): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $array_sql = [
      'question' => $question,
      'response' => $result,
      'date_added' => 'now()',
      'user_admin' => 'Chatbot Front Office'
    ];

    $CLICSHOPPING_Db->save('gpt', $array_sql);

    $QlastId = $CLICSHOPPING_Db->prepare('select gpt_id
                                           from :table_gpt
                                           order by gpt_id desc
                                           limit 1
                                          ');
    $QlastId->execute();

    $modelArray = self::getGptModel(); // Get the array of models
    $modelId = $modelArray[0]['id']; // Get the 'id' of the first model
    $engine = $modelId; // Assign the model ID to the $engine variable

    $array_usage_sql = [
      'gpt_id' => $QlastId->valueInt('gpt_id'),
      'promptTokens' => $usage['promptTokens'],
      'completionTokens' => $usage['completionTokens'],
      'totalTokens' => $usage['totalTokens'],
      'ia_type' => 'GPT',
      'model' => $engine,
      'date_added' => 'now()'
    ];

    $CLICSHOPPING_Db->save('gpt_usage', $array_usage_sql);
  }
}