<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\ChatGpt\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;
  use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Chat;

  use OpenAI;
  use OpenAI\Exceptions\ErrorException;


  class Insert implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('ChatGpt')) {
        Registry::set('ChatGpt', new ChatGptApp());
      }

      $this->app = Registry::get('ChatGpt');

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/seo_chat_gpt');
    }

    /**
     * @param string $question
     * @return bool|string
     * @throws \Exception
     */
    public static function getChatGptResponse(string $question) :bool|string
    {
     if (Chat::checkGotStatus() === false) {
       return false;
     }

      $client = OpenAI::client(CLICSHOPPING_APP_CHATGPT_CH_API_KEY);
      $prompt = HTML::sanitize($question);
      $engine = CLICSHOPPING_APP_CHATGPT_CH_MODEL;

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

        return $result;
      }catch (\RuntimeException $e) {
        throw new \Exception('Error appears, please look the console error');
        return false;
      }
    }


    public function execute()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!\defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') || CLICSHOPPING_APP_CHATGPT_CH_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Insert'], $_GET['Products'])) {
        $question = $this->app->getDef('text_seo_page_title_question');
        $question_tag = $this->app->getDef('text_seo_page_tag_question');
        $question_keywords = $this->app->getDef('text_seo_page_keywords_question');
        $question_summary_description = $this->app->getDef('text_seo_page_summary_description_question');
        $translate_language = $this->app->getDef('text_seo_page_translate_language');

        $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');

        $Qcheck = $this->app->db->prepare('select products_id
                                            from :table_products
                                            order by products_id desc
                                            limit 1
                                          ');
        $Qcheck->execute();

        if ($Qcheck->valueInt('products_id') !== null) {
          $Qproducts = $this->app->db->prepare('select products_id,
                                                       products_name,
                                                       language_id
                                                from :table_products_description
                                                where products_id = :products_id
                                              ');
          $Qproducts->bindInt(':products_id', $Qcheck->valueInt('products_id'));
          $Qproducts->execute();

          $products_array = $Qproducts->fetchAll();

          foreach ($products_array as $item) {
            $product_name = $CLICSHOPPING_ProductsAdmin->getProductsName($item['products_id']);
            $language_name = $CLICSHOPPING_Language->getLanguagesName($item['language_id']);

            $update_sql_data = [
              'language_id' => $item['language_id'],
              'products_id' => $item['products_id']
            ];

//-------------------
// products description
//-------------------
            if(isset($_POST['option_gpt_description'])) {
              $question_description = 'Décris moi une longue description concernant ce produit :';
              $technical_question = 'puis créés moi un nouveau paragraphe avec des bullets points en html concernant les caractéristiques techniques';

              $products_description =  $translate_language . ' ' . $language_name . ' : ' .  $question_description . ' ' . $product_name . ' ' . $technical_question;
              $products_description = static::getChatGptResponse($products_description);

              if ($products_description !== false) {
                $sql_data_array = [
                  'products_description' => $products_description ?? '',
                ];

                $this->app->db->save('products_description', $sql_data_array, $update_sql_data);
              }
            }
//-------------------
// Summary description
//-------------------
            if(isset($_POST['option_gpt_summary_description'])) {
              $summary_description = $translate_language . ' ' . $language_name . ' : ' . $question_summary_description . ' ' . $product_name;
              $summary_description = static::getChatGptResponse($summary_description);

              if ($summary_description !== false) {
                $sql_data_array = [
                  'products_description_summary' => $summary_description ?? '',
                ];

                $this->app->db->save('products_description', $sql_data_array, $update_sql_data);
              }
            }
////-------------------
// Seo Title
//-------------------
            if(isset($_POST['option_gpt_seo_title'])) {
              $seo_product_title = $translate_language . ' ' . $language_name . ' : ' . $question . ' ' . $product_name;
              $seo_product_title = static::getChatGptResponse($seo_product_title);

              if ($seo_product_title !== false) {
                $sql_data_array = [
                  'products_head_title_tag' => $seo_product_title ?? '',
                ];

                $this->app->db->save('products_description', $sql_data_array, $update_sql_data);
              }
            }
//-------------------
// Seo description
//-------------------
            if(isset($_POST['option_gpt_seo_title'])) {
              $seo_product_description = $translate_language . ' ' . $language_name . ' : ' . $question_summary_description . ' ' . $product_name;
              $seo_product_description = static::getChatGptResponse($seo_product_description);

              if ($seo_product_description !== false) {
                $sql_data_array = [
                  'products_head_desc_tag' => $seo_product_description ?? '',
                ];

                $this->app->db->save('products_description', $sql_data_array, $update_sql_data);
              }
            }
//-------------------
// Seo keywords
//-------------------
            if(isset($_POST['option_gpt_seo_keywords'])) {
              $seo_product_keywords = $translate_language . ' ' . $language_name . ' : ' . $question_keywords . ' ' . $product_name;
              $seo_product_keywords = static::getChatGptResponse($seo_product_keywords);

              if ($seo_product_keywords !== false) {
                $sql_data_array = [
                  'products_head_keywords_tag' => $seo_product_keywords ?? '',
                ];

                $this->app->db->save('products_description', $sql_data_array, $update_sql_data);
              }
            }
//-------------------
// Seo tag
//-------------------
            if(isset($_POST['option_gpt_seo_tags'])) {
              $seo_product_tag =  $translate_language. ' ' . $language_name . ' : ' .  $question_tag . ' ' . $product_name;
              $seo_product_tag = static::getChatGptResponse($seo_product_tag);

              if ($seo_product_tag !== false) {
                $sql_data_array = [
                  'products_head_tag' => $seo_product_tag ?? '',
                ];

                $this->app->db->save('products_description', $sql_data_array, $update_sql_data);
              }
            }
          }
        }
      }
    }
  }