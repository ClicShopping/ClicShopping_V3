<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;
use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

use LLPhant\Chat\OllamaChat;
use LLPhant\Chat\OpenAIChat;
use LLPhant\Exception\MissingParameterExcetion;
use LLPhant\OpenAIConfig;
use LLPhant\OllamaConfig;
use LLPhant\Chat\TokenUsage;
use LLPhant\AnthropicConfig;
use LLPhant\Chat\AnthropicChat;

use function defined;
use function is_null;

class Gpt {
  public function __construct() {
  }
  
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
  public static function getGptModel(): array
  {
    $array = [
      ['id' => 'gpt-3.5-turbo', 'text' => 'OpenAI gpt-3.5-turbo'],
      ['id' => 'gpt-4o', 'text' => 'OpenAi gpt-4o'],
      ['id' => 'gemma:7b', 'text' => 'Ollama Gemma:7b'],
      ['id' => 'mistral:7b', 'text' => 'Ollama Mistral:7b'],
      ['id' => 'anth-sonnet', 'text' => 'Anthropic Claude Sonnet 3.5'],
      ['id' => 'anth-opus', 'text' => 'Anthropic Claude Opus'],
      ['id' => 'anth-haiku', 'text' => 'Anthropic Claude Haiku'],
    ];

    return $array;
  }

  /**
   * @return string
   */
  public static function getGptModalMenu(): string
  {
    $array = self::getGptModel();

    $menu = HTML::selectField('engine', $array, null, 'id="engine"');

    return $menu;
  }

  /**
   * @param array|null $parameters
   * @return array
   * @throws \Exception
   */
  public static function getOpenAiGpt(array|null $parameters): mixed
  {

     $config = new OpenAIConfig();
     $config->apiKey = CLICSHOPPING_APP_CHATGPT_CH_API_KEY;

     if (!is_null($parameters)) {
        $config->model = $parameters['model'];
        $config->modelOptions = $parameters;
      }

      $chat = new OpenAIChat($config);

      return $chat;
    }

  /**
   * @param string $question
   * @param int|null $maxtoken
   * @param float|null $temperature
   * @param string|null $engine
   * @param int|null $max
   * @return mixed
   * @throws \Exception
   */
   public static function getOpenAIChat(string $question, ?int $maxtoken = null, ?float $temperature = null, ?string $engine = null, ?int $max = 1): mixed
   {
    if (!empty(CLICSHOPPING_APP_CHATGPT_CH_API_KEY)) {
      $top = ['\n'];

      if (is_null($maxtoken)) {
        $maxtoken = (int)CLICSHOPPING_APP_CHATGPT_CH_MAX_TOKEN;
      }

      if (is_null($temperature)) {
        $temperature = (float)CLICSHOPPING_APP_CHATGPT_CH_TEMPERATURE;
      }

      if (is_null($max)) {
        $max = (float)CLICSHOPPING_APP_CHATGPT_CH_MAX_RESPONSE;
      }

      $parameters = [
        'temperature' => $temperature, // Contrôle de la créativité du modèle
        'top_p' => (float)CLICSHOPPING_APP_CHATGPT_CH_TOP_P,
        'frequency_penalty' => (float)CLICSHOPPING_APP_CHATGPT_CH_FREQUENCY_PENALITY, //pénalité de fréquence pour encourager le modèle à générer des réponses plus variées
        'presence_penalty' => (float)CLICSHOPPING_APP_CHATGPT_CH_PRESENCE_PENALITY, //pénalité de présence pour encourager le modèle à générer des réponses avec des mots qui n'ont pas été utilisés dans l'amorce
        'max_tokens' => $maxtoken, //nombre maximum de jetons à générer dans la réponse
        'stop' => $top, //caractères pour arrêter la réponse
        'n' => $max, // nombre de réponses à générer
        'user' => AdministratorAdmin::getUserAdmin(), // nom de l'utilisateur
        'response_format' => (object)['type' => CLICSHOPPING_APP_CHATGPT_CH_RESPONSE_FORMAT], // Request JSON object response
        'messages' => [
                        'role' => 'system',
                        'content' => 'You are an e-commerce expert in marketing.'
                      ]
      ];

      if (!empty(CLICSHOPPING_APP_CHATGPT_CH_ORGANIZATION)) {
        $parameters['organization'] = CLICSHOPPING_APP_CHATGPT_CH_ORGANISATION;
      }

      if (!\is_null($engine)) {
        $parameters['model'] = $engine;
      }

      $chat = self::getOpenAiGpt($parameters);

      return $chat;
    } else {
      return false;
    }
  }

  /**
   * @param string $model
   * @return mixed
   * @throws MissingParameterExcetion
   */
  public static function getOllamaChat(string $model = 'mistral:7b'): mixed
  {
      $config = new OllamaConfig();
      $config->model = $model;
      $chat = new OllamaChat($config);

      return $chat;
  }


  /**
   * @param string $model
   * @param int|null $maxtoken
   * @param array|null $modelOptions
   * @return array
   */
  public static function getAnthropicChat(string $model, int|null $maxtoken = null, array|null $modelOptions = null): mixed
  {
    $api_key = CLICSHOPPING_APP_CHATGPT_CH_API_KEY_ANTHROPIC;
    $chat = false;

    if ($modelOptions === null) {
      $modelOptions = [
        'messages' => [
          ['role' => 'system',
           'content' => 'You are an e-commerce expert in marketing.'
          ]
        ]
      ];
    }
/*
    // Perform the transformation if needed, e.g., extract contents from messages
    $transformedMessages = array_map(function($message) {
      return $message['content'];
    }, $modelOptions['messages']);

    // If transformation means a different structure, adapt accordingly
    // For example, if you want to convert it to a dictionary:
    $modelOptions['transformedMessages'] = $transformedMessages;
*/
    if (is_null($maxtoken)) {
      $maxtoken = (int)CLICSHOPPING_APP_CHATGPT_CH_MAX_TOKEN;
    }


    // Create a new AnthropicChat instance with the specified configuration

    if (!empty(CLICSHOPPING_APP_CHATGPT_CH_API_KEY_ANTHROPIC)) {
      if ($model = 'anth-sonnet') {
        $chat = new AnthropicChat(new AnthropicConfig(AnthropicConfig::CLAUDE_3_5_SONNET, $maxtoken, $modelOptions, $api_key));
      } elseif ($model = 'anth-opus') {
        $chat = new AnthropicChat(new AnthropicConfig(AnthropicConfig::CLAUDE_3_OPUS, $maxtoken, $modelOptions, $api_key));
      } else {
        $chat = new AnthropicChat(new AnthropicConfig(AnthropicConfig::CLAUDE_3_HAIKU, $maxtoken, $modelOptions, $api_key));
      }

      return $chat;
    } else {
      return false;
    }
  }

  /**
   * @param string $question
   * @param int|null $maxtoken
   * @param float|null $temperature
   * @param string|null $engine
   * @param int|null $max
   * @return mixed
   * @throws MissingParameterExcetion
   */
  private static function getChat(string $question, ?int $maxtoken = null, ?float $temperature = null, ?string $engine = null, ?int $max = 1): mixed
  {
    if (strpos(CLICSHOPPING_APP_CHATGPT_CH_MODEL, 'gpt') === 0) {
      $client = self::getOpenAIChat($question, $maxtoken, $temperature, $engine, $max);
    } elseif (strpos(CLICSHOPPING_APP_CHATGPT_CH_MODEL, 'anth') === 0) {
       $client = self::getAnthropicChat(CLICSHOPPING_APP_CHATGPT_CH_MODEL, $maxtoken);
    } else {
      $client = self::getOllamaChat(CLICSHOPPING_APP_CHATGPT_CH_MODEL);
    }

    return $client;
  }

  /**
   * @param string $question
   * @param int|null $maxtoken
   * @param float|null $temperature
   * @param string|null $engine
   * @param int|null $max
   * @return bool|string
   */
  public static function getGptResponse(string $question, ?int $maxtoken = null, ?float $temperature = null, ?string $engine = null, ?int $max = 1): bool|string
  {
    if (self::checkGptStatus() === false) {
      return false;
    }

    if (is_null($engine)) {
      $engine = CLICSHOPPING_APP_CHATGPT_CH_MODEL;
    }

    $prompt = HTML::sanitize($question);

    // Get the chat instance
    $chat = self::getChat($question, $maxtoken, $temperature, $engine, $max);

    // Generate text using the chat instance
    $result = $chat->generateText($prompt);

    if (strpos(CLICSHOPPING_APP_CHATGPT_CH_MODEL, 'gpt') === 0) {
      $lastResponse = $chat->getLastResponse();

      if (!is_null($lastResponse)) {
        $usage = [
          'prompt_tokens' => $lastResponse['usage']['prompt_tokens'],
          'completion_tokens' => $lastResponse['usage']['completion_tokens'],
          'total_tokens' => $lastResponse['usage']['total_tokens']
        ];
      } else {
        $usage = null;
      }

      self::saveData($question, $result, $engine, $usage);
    }

    return $result;
  }

  /**
   * @param string $question
   * @param string $result
   * @param string|null $engine
   * @param array|null $usage
   */
  private static function saveData(string $question, string $result, string|null $engine, array|null $usage): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $promptTokens = 0;
    $completionTokens = 0;
    $totalTokens = 0;

    $array_sql = [
      'question' => $question,
      'response' => $result,
      'date_added' => 'now()',
      'user_admin' => 'Chatbot ' . $engine
    ];

    $CLICSHOPPING_Db->save('gpt', $array_sql);

    $QlastId = $CLICSHOPPING_Db->prepare('select gpt_id
                                           from :table_gpt
                                           order by gpt_id desc
                                           limit 1
                                          ');
    $QlastId->execute();

    if (!is_null($usage)) {
      $promptTokens = $usage['prompt_tokens'];
      $completionTokens = $usage['completion_tokens'];
      $totalTokens = $usage['total_tokens'];
    }

    $array_usage_sql = [
      'gpt_id' => $QlastId->valueInt('gpt_id'),
      'promptTokens' => $promptTokens, // Accéder à la valeur de 'prompt_tokens'
      'completionTokens' => $completionTokens, // Accéder à la valeur de 'completion_tokens'
      'totalTokens' => $totalTokens, // Accéder à la valeur de 'total_tokens
      'ia_type' => 'GPT',
      'model' => $engine,
      'date_added' => 'now()'
    ];

    $CLICSHOPPING_Db->save('gpt_usage', $array_usage_sql);
  }
  
  /*****************************************
   * Statistiques
   ****************************************/

  /**
   * @return array
   */
  public static function getTotalTokenByMonth(): array
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qtotal = $CLICSHOPPING_Db->prepare('select sum(promptTokens) as promptTokens,
                                                  sum(completionTokens) as completionTokens,
                                                  sum(totalTokens) as totalTokens,
                                                  date_added
                                           from :table_gpt_usage
                                           where DATE_SUB(NOW(), INTERVAL 1 MONTH)
                                          ');
    $Qtotal->execute();

    $result = $Qtotal->fetch();

    return $result;
  }

  /**
   * @return array
   */
  public static function getTokenbyId(int $id): array
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qtotal = $CLICSHOPPING_Db->prepare('select sum(promptTokens) as promptTokens,
                                                  sum(completionTokens) as completionTokens,
                                                  sum(totalTokens) as totalTokens,
                                                  date_added
                                           from :table_gpt_usage
                                           where gpt_id = :gpt_id
                                          ');
    $Qtotal->binInt(':gtp_id', $id);
    $Qtotal->execute();

    $result = $Qtotal->fetch();

    return $result;
  }

  /**
   * @return bool|float
   */
  public static function getErrorRateGpt(): bool|float
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qtotal = $CLICSHOPPING_Db->prepare('select count(gpt_id) as total_id
                                           from :table_gpt
                                          ');
    $Qtotal->execute();

    $result_total_chat = $Qtotal->valueInt('avg');

    $QtotalResponse = $CLICSHOPPING_Db->prepare('select count(response) as total
                                                   from :table_gpt
                                                   where (response like :response or response like :response1)
                                                   and user_admin like :user_admin
                                                  ');
    $QtotalResponse->bindValue(':response', '%I\'m sorry but I do not find%');
    $QtotalResponse->bindValue(':response1', '%Je suis désolé mais je n\'ai pas trouvé d\'informations%');
    $QtotalResponse->bindValue(':user_admin', '%Chatbot Front Office%');

    $QtotalResponse->execute();

    $result_no_response = $QtotalResponse->valueDecimal('total');

    if ($result_no_response > 0) {
      $result = ($result_no_response / $result_total_chat) * 100 . '%';
    } else {
      $result = false;
    }

    return $result;
  }

  /**
   * @return String
   */
  public static function gptModalMenu(): string
  {
    $menu = '';

    if (defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') && CLICSHOPPING_APP_CHATGPT_CH_STATUS == 'True' && !empty(CLICSHOPPING_APP_CHATGPT_CH_API_KEY)) {
      $menu .= '
              <span class="col-md-2">
                <!-- Modal -->
                <a href="#chatModal" data-bs-toggle="modal" data-bs-target="#chatModal"><span class="text-white"><i class="bi bi-chat-left-dots-fill" title="' . CLICSHOPPING::getDef('text_chat_open') . '"></i><span></a>
                <div class="modal fade" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="chatModalLabel">' . CLICSHOPPING::getDef('text_chat_title') . '</h5>
                        <div class="ms-auto">
                            ' . HTML::button(CLICSHOPPING::getDef('text_chat_close'), null, null, 'secondary', ['params' => 'data-bs-dismiss="modal"']) . '
                        </div>
                      </div>
                      <div class="modal-body">
                        <div class="mt-1"></div>

                        <div class="mt-1"></div>
                        <div class="form-group">
                          <textarea class="form-control" id="messageGpt" rows="3" placeholder="' . CLICSHOPPING::getDef('text_chat_message') . '"></textarea>
                        </div>
                        <div class="mt-1"></div>
                        <div class="form-group text-end">
                          <div class="row">
                            <span class="col-md-6 text-start">
                              <ul class="list-group-slider list-group-flush">
                                <span class="text-slider col-6">' . CLICSHOPPING::getDef('text_chat_save') . '</span>
                                <li class="list-group-item-slider">
                                  <label class="switch">
                                    ' . HTML::checkboxField('saveGpt', null, 0, 'class="success" id="saveGpt"') . '
                                    <span class="slider"></span>
                                  </label>
                                </li>
                              </ul>
                            </span>
                            <span class="col-md-6 text-end">                          
                               ' . HTML::button(CLICSHOPPING::getDef('text_chat_send'), null, null, 'primary', ['params' => 'id="sendGpt"'], 'sm') . '
                            </span>                         
                          </div>
                        </div>
                        <div class="mt-1"></div>
                        <div class="card">
                          <div class="input-group">
                            <div class="chat-box-message text-start">
                              <div id="chatGpt-output" class="text-bg-light"></div>
                              <div class="mt-1"></div>
                              <div class="col-md-12">
                                <div class="row">
                                  <span class="col-md-12">
                                    <button id="copyResultButton" class="btn btn-primary btn-sm d-none" data-clipboard-target="#chatGpt-output">
                                      <i class="bi bi-clipboard" title="' . CLICSHOPPING::getDef('text_copy') . '"></i> ' . CLICSHOPPING::getDef('text_copy') . '
                                    </button>
                                  </span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>  
                      </div>
                    </div>
                  </div>
                </div>
              </span>
          ';
    }

    return $menu;
  }
  
  /*****************************************
   * Ckeditor
   ****************************************/

  /**
   * @return string
   */
  public static function gptCkeditorParameters(): string|bool
  {
    $model = CLICSHOPPING_APP_CHATGPT_CH_MODEL;

    $url = "https://api.openai.com/v1/chat/completions";

    $organization = '';
    if (!empty(CLICSHOPPING_APP_CHATGPT_CH_ORGANIZATION)) {
      $organization = 'let organizationGpt = "' . CLICSHOPPING_APP_CHATGPT_CH_ORGANIZATION . '"';
    }

    $script = '<script>
     let apiGptUrl = "' . $url . '";
     let apiKeyGpt = "' . CLICSHOPPING_APP_CHATGPT_CH_API_KEY . '";
     ' . $organization . ';
     let modelGpt =  "' . $model . '";
     let frequency_penalty_gpt = parseFloat("' . (float)CLICSHOPPING_APP_CHATGPT_CH_FREQUENCY_PENALITY . '");
     let presence_penalty_gpt = parseFloat("' . (float)CLICSHOPPING_APP_CHATGPT_CH_PRESENCE_PENALITY . '");
     let max_tokens_gpt = parseInt("' . (int)CLICSHOPPING_APP_CHATGPT_CH_MAX_TOKEN . '");
     let temperatureGpt = parseFloat("' . (float)CLICSHOPPING_APP_CHATGPT_CH_TEMPERATURE . '");
     let nGpt = parseInt("' . (int)CLICSHOPPING_APP_CHATGPT_CH_MAX_RESPONSE . '");
     let best_of_gpt = parseInt("' . (int)CLICSHOPPING_APP_CHATGPT_CH_BESTOFF . '");
     let top_p_gpt =  parseFloat("' . (float)CLICSHOPPING_APP_CHATGPT_CH_TOP_P . '");
     let titleGpt = "' . CLICSHOPPING::getDef('text_chat_title') . '";
    </script>';

    $script .= '<!--start wysiwig preloader--><style>.blur {filter: blur(1px);opacity: 0.4;}</style><!--end wysiwzg preloader-->';
    $script .= '<script src="' . CLICSHOPPING::link('Shop/ext/javascript/cKeditor/dialogs/chatgpt.js') . '"></script>';

    return $script;
  }
}