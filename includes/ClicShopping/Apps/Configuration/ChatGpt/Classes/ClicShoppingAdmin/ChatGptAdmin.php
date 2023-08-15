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
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  use OpenAI;

  class ChatGptAdmin
  {
     public function __construct()
    {
    }

    /**
     * @return bool
     */
    public static function checkGptStatus() :bool
    {
      if (!\defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') || CLICSHOPPING_APP_CHATGPT_CH_STATUS == 'False' || empty('CLICSHOPPING_APP_CHATGPT_CH_API_KEY')) {
        return false;
      } else {
        return true;
      }
    }

    /**
     * @param bool $chatGpt
     * @return string
     */
    public static function getAjaxUrl(bool $chatGpt = true) :string
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
    public static function getAjaxSeoMultilanguageUrl() :string
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
        ['id' => 'text-davinci-003',
         'text' =>'Davinci (text-davinci-003)'
        ],
      ];

      return $array;
    }

    /**
     * @return array
     */
    public static function getGptModalMenu(): string
    {
      $array = static::getGptModel();

      $menu = HTML::selectField('engine', $array, null, 'id="engine"');

      return $menu;
    }

    /**
     * @return string
     */
    public static function gptCkeditorParameters(): string
    {
      if (!empty(CLICSHOPPING_APP_CHATGPT_CH_ORGANIZATION)) {
        $organization = 'let organizationGpt = "' . CLICSHOPPING_APP_CHATGPT_CH_ORGANIZATION . '"';
      } else {
        $organization = '';
      }

      $script = '<script>
       let apiKeyGpt = "' .  CLICSHOPPING_APP_CHATGPT_CH_API_KEY . '";
       ' . $organization . ';
       let modelGpt = "' .  CLICSHOPPING_APP_CHATGPT_CH_MODEL . '";
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
      $script .= '<script src="' . CLICSHOPPING::link('Shop/ext/javascript/cKeditor/dialogs/chatgpt.js') .'"></script>';

      return $script;
    }

    /**
     * @return OpenAI\Client
     */
    public static function getClient()
    {
      if (!empty(CLICSHOPPING_APP_CHATGPT_CH_API_KEY)) {
        $client = OpenAI::client(CLICSHOPPING_APP_CHATGPT_CH_API_KEY);

        return $client;
      }  else {
        return false;
      }
    }

    /**
     * @param string $question
     * @return bool|string
     * @throws \Exception
     */
    public static function getGptResponse(string $question, ?int $max_tokens = null, ?float $temperature = null) :bool|string
    {
      if (ChatGptAdmin::checkGptStatus() === false) {
        return false;
      }

      $client = static::getClient();

      $prompt = HTML::sanitize($question);
      $engine = CLICSHOPPING_APP_CHATGPT_CH_MODEL;

      $top = ['\n'];

      if (is_null($max_tokens)) {
        $max_tokens = (int)CLICSHOPPING_APP_CHATGPT_CH_MAX_TOKEN;
      }

      if (is_null($temperature)) {
        $temperature = (float)CLICSHOPPING_APP_CHATGPT_CH_TEMPERATURE;
      }

      $parameters = [
        'model' => $engine,  // Spécification du modèle à utiliser
        'temperature' => $temperature, // Contrôle de la créativité du modèle
        'top_p' => (float)CLICSHOPPING_APP_CHATGPT_CH_TOP_P , // Caractère de fin de ligne pour la réponse
        'frequency_penalty' => (float)CLICSHOPPING_APP_CHATGPT_CH_FREQUENCY_PENALITY, //pénalité de fréquence pour encourager le modèle à générer des réponses plus variées
        'presence_penalty' => (float)CLICSHOPPING_APP_CHATGPT_CH_PRESENCE_PENALITY, //pénalité de présence pour encourager le modèle à générer des réponses avec des mots qui n'ont pas été utilisés dans l'amorce
        'prompt' => $prompt, // Texte d'amorce
        'max_tokens' => $max_tokens, //nombre maximum de jetons à générer dans la réponse
        'stop' => $top, //caractères pour arrêter la réponse
        'n' => (int)CLICSHOPPING_APP_CHATGPT_CH_MAX_RESPONSE, // nombre de réponses à générer
        'best_of' => (int)CLICSHOPPING_APP_CHATGPT_CH_BESTOFF, //Generates best_of completions server-side and returns the "best"
      ];

      if (!empty(CLICSHOPPING_APP_CHATGPT_CH_ORGANIZATION)) {
        $parameters = [
          'organization' => CLICSHOPPING_APP_CHATGPT_CH_ORGANISATION,
        ];
      }

      $response = $client->completions()->create($parameters);

      try {
        $result = $response['choices'][0]['text'];

        $array_usage = [
          'promptTokens' => $response->usage->promptTokens,
          'completionTokens' => $response->usage->completionTokens,
          'totalTokens' => $response->usage->totalTokens,
        ];

        static::saveData($prompt, $result, $array_usage);

        return $result;
      }catch (\RuntimeException $e) {
        throw new \Exception('Error appears, please look the console error');

        return false;
      }
    }

    /**
     * @param string $prompt
     * @param string $result
     * @param array $usage
     */
    public static function saveData(string $prompt, string $result, array $usage) :void
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $array_sql = [
        'question' => $prompt,
        'response' => $result,
        'date_added' => 'now()',
        'user_admin' => AdministratorAdmin::getUserAdmin()
      ];

      $CLICSHOPPING_Db->save('gpt', $array_sql);

      $QlastId = $CLICSHOPPING_Db->prepare('select gpt_id
                                           from :table_gpt
                                           order by gpt_id desc
                                           limit 1
                                          ');
      $QlastId->execute();

      $array_usage_sql = [
        'gpt_id' => $QlastId->valueInt('gpt_id'),
        'promptTokens' => $usage['promptTokens'],
        'completionTokens' => $usage['completionTokens'],
        'totalTokens' => $usage['totalTokens'],
        'ia_type' => 'GPT',
        'model' => CLICSHOPPING_APP_CHATGPT_CH_MODEL,
        'date_added' => 'now()'
      ];

      $CLICSHOPPING_Db->save('gpt_usage', $array_usage_sql);
    }

    /**
     * @return array
     */
    public static function getTotalTokenByMonth() :array
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
    public static function getTokenbyId(int $id) :array
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
                                                   where response like :response
                                                   and user_admin like :user_admin
                                                  ');
      $QtotalResponse->bindValue(':response', '%I\'m sorry but I do not find%');
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
     * @param string $file
     * @return array
     */
    public static function upLoadFile(string $file, string $purpose = 'fine-tune') :array
    {
      $client = static::getClient();

      $array = [
          'purpose' => $purpose,
          'file' => fopen($file, 'r'),
      ];

      $response = $client->files()->upload($array);

      return $response;
    }

    /**
     * @param string $file
     * @return array
     */
    public static function deleteFile(string $file) :array
    {
      $client = static::getClient();

      $response = $client->files()->delete($file);

      return $response;
    }

    /**
     * @param string $file
     * @return array
     */
    public static function retrieveFile(string $file) :array
    {
      $client = static::getClient();

      $response = $client->files()->retrieve($file);

      return $response;
    }


    /**
     * @param string $file
     * @return array
     */
    public static function downloadFile(string $file) :array
    {
      $client = static::getClient();

      $response = $client->files()->download($file);

      return $response;
    }

    /**
     * @param string $name
     * @param string $directory
     * @param string $size
     * @param bool $rename
     * @return array|false
     */
    public static function createImageChatGpt(string $name, string $directory = 'products', string $size = '256x256', bool $rename = false) :string|bool
    {
      if (ChatGptAdmin::checkGptStatus() === false) {
        return false;
      }

      $template_image_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/images/' . $directory . '/';

      $client = static::getClient();

      $array = [
        'prompt' => $name,
        'n' => 1,
        'size' => $size,
        'response_format' => 'url',
      ];
      
      $response = $client->images()->create($array);

      if (!\is_null($response->created)) {
        foreach ($response->data as $data) {
          $url_image = file_get_contents($data->url);
          $image_name = HTML::removeFileAccents($name);

          if ($rename === true) {
            $image_name = str_replace(' ', '_', $image_name);
            $rand = rand(1,20);
            $image_name = $image_name . '_' . $rand;
          } else {
            $image_name = str_replace(' ', '_', $image_name);
          }

          $directory_image = $template_image_directory . $image_name . '.jpg';
          file_put_contents($directory_image, $url_image);
        }
      }

      if (file_get_contents($template_image_directory . $image_name . '.jpg') !== null) {
        $save_image =  $directory . '/' . $image_name . '.jpg';

        return $save_image;
      } else {
        return false;
      }
    }

    /**
     * @return String
     */
    public static function gptModalMenu() : String
    {
      $menu = '';

      if (\defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') && CLICSHOPPING_APP_CHATGPT_CH_STATUS =='True' && !empty(CLICSHOPPING_APP_CHATGPT_CH_API_KEY)) {
        $menu .= '
              <span class="col-md-2">
                <!-- Modal -->
                <a href="#chatModal" data-bs-toggle="modal" data-bs-target="#chatModal"><span class="text-white"><i class="bi bi-chat-left-dots-fill" title="' . CLICSHOPPING::getDef('text_chat_open') . '"></i><span></a>
                <div class="modal fade" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="chatModalLabel">' . CLICSHOPPING::getDef('text_chat_title') . '</h5>
                        ' . HTML::button(CLICSHOPPING::getDef('text_chat_close'), null, null, 'secondary', ['params' => 'data-bs-dismiss="modal"'] ) . '
                      </div>
                      <div class="modal-body">
                        <div class="separator"></div>

                        <div class="separator"></div>
                        <div class="form-group">
                          <textarea class="form-control" id="messageGpt" rows="3" placeholder="' . CLICSHOPPING::getDef('text_chat_message') . '"></textarea>
                        </div>
                        <div class="separator"></div>
                        <div class="form-group text-end">
                          <div class="row">
                            <span class="col-md-6 text-start">
                              <ul class="list-group-slider list-group-flush">
                                <span class="text-slider col-6">' . CLICSHOPPING::getDef('text_chat_save') . '</span>
                                <li class="list-group-item-slider">
                                  <label class="switch">
                                    ' . HTML::checkboxField('saveGpt', '1', 0, 'class="success" id="saveGpt"') . '
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
                        <div class="separator"></div>
                        <div class="card">
                          <div class="input-group">
                            <div class="chat-box-message text-start">
                              <div id="chatGpt-output" class="text-bg-light"></div>
                              <div class="separator"></div>
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
  }