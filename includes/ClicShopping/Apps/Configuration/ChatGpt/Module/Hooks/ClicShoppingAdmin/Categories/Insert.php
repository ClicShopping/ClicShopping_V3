<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Module\Hooks\ClicShoppingAdmin\Categories;

use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\NewVector;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Gpt;

class Insert implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  /**
   * Class constructor.
   *
   * Initializes the ChatGptApp instance in the Registry if it doesn't already exist,
   * and loads the necessary definitions for the application.
   *
   * @return void
   */
  public function __construct()
  {
    if (!Registry::exists('ChatGpt')) {
      Registry::set('ChatGpt', new ChatGptApp());
    }

    $this->app = Registry::get('ChatGpt');

    $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Categories/seo_chat_gpt');
  }

  /**
   * Executes the necessary processes based on the provided GET and POST parameters related to category handling.
   *
   * Checks if GPT functionality is enabled and processes category-related inputs to update database records
   * such as descriptions, SEO data (title, description, keywords), and optionally images.
   *
   * @return bool Returns false if GPT functionality is disabled or not applicable; otherwise, performs the operations without returning a value.
   */
  public function execute()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    if (Gpt::checkGptStatus() === false) {
      return false;
    }

    if (isset($_GET['Insert'], $_GET['Categories'])) {
      $translate_language = $this->app->getDef('text_seo_page_translate_language');

      $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');

      $Qcheck = $this->app->db->prepare('select categories_id
                                            from :table_categories
                                            order by categories_id desc
                                            limit 1
                                          ');
      $Qcheck->execute();

      if ($Qcheck->valueInt('categories_id') !== null) {
        $Qcategories = $this->app->db->prepare('select categories_id,
                                                   categories_name,
                                                   language_id
                                             from :table_categories_description
                                             where categories_id = :categories_id
                                            ');
        $Qcategories->bindInt(':categories_id', $Qcheck->valueInt('categories_id'));
        $Qcategories->execute();

        $categories_array = $Qcategories->fetchAll();

        foreach ($categories_array as $item) {
          $categories_name = $CLICSHOPPING_CategoriesAdmin->getCategoryName($item['categories_id'], $item['language_id']);
          $language_name = $CLICSHOPPING_Language->getLanguagesName($item['language_id']);

          $update_sql_data = [
            'language_id' => $item['language_id'],
            'categories_id' => $item['categories_id']
          ];

//-------------------
// categories description
//-------------------
          $categories_description = '';
          if (isset($_POST['option_gpt_description'])) {
            $question_description = $this->app->getDef('text_categories_description', ['category_name' => $categories_name]);
            $categories_description = $translate_language . ' ' . $language_name . ' ' . $question_description;
            $categories_description = Gpt::getGptResponse($categories_description);

            if ($categories_description !== false) {
              $sql_data_array = [
                'categories_description' => $categories_description ?? '',
              ];

              $this->app->db->save('categories_description', $sql_data_array, $update_sql_data);
            }
          }
////-------------------
// Seo Title
//-------------------
          $seo_categories_title = '';
          if (isset($_POST['option_gpt_seo_title'])) {
            $question = $this->app->getDef('text_seo_page_title_question', ['category_name' => $categories_name]);

            $seo_categories_title = $translate_language . ' ' . $language_name . ' : ' . $question;
            $seo_categories_title = Gpt::getGptResponse($seo_categories_title);

            if ($seo_categories_title !== false) {
              $sql_data_array = [
                'categories_head_title_tag' => strip_tags($seo_categories_title) ?? '',
              ];

              $this->app->db->save('categories_description', $sql_data_array, $update_sql_data);
            }
          }
//-------------------
// Seo description
//-------------------
          $seo_categories_description = '';
          if (isset($_POST['option_gpt_seo_title'])) {
            $question_summary_description = $this->app->getDef('text_seo_page_summary_description_question', ['category_name' => $categories_name]);

            $seo_categories_description = $translate_language . ' ' . $language_name . ' : ' . $question_summary_description;
            $seo_categories_description = Gpt::getGptResponse($seo_categories_description);

            if ($seo_categories_description !== false) {
              $sql_data_array = [
                'categories_head_desc_tag' => strip_tags($seo_categories_description) ?? '',
              ];

              $this->app->db->save('categories_description', $sql_data_array, $update_sql_data);
            }
          }
//-------------------
// Seo keywords
//-------------------
          $seo_categories_keywords = '';
          if (isset($_POST['option_gpt_seo_keywords'])) {
            $question_keywords = $this->app->getDef('text_seo_page_keywords_question', ['category_name' => $categories_name]);

            $seo_categories_keywords = $translate_language . ' ' . $language_name . ' : ' . $question_keywords;
            $seo_categories_keywords = Gpt::getGptResponse($seo_categories_keywords);

            if ($seo_categories_keywords !== false) {
              $sql_data_array = [
                'categories_head_keywords_tag' => strip_tags($seo_categories_keywords) ?? '',
              ];

              $this->app->db->save('categories_description', $sql_data_array, $update_sql_data);
            }
          }

//********************
// add embedding
//********************
          if (CLICSHOPPING_APP_CHATGPT_CH_OPENAI_EMBEDDING == 'True') {
            $embedding_data = "Category Name: $categories_name\n";

            if (!empty($categories_description)) {
              $embedding_data .= "Category Description: $categories_description\n";
            }

            if (!empty($seo_categories_title)) {
              $embedding_data .= "Category SEO Title: $seo_categories_title\n";
            }

            if (!empty($seo_categories_description)) {
              $embedding_data .= "Category SEO Description: $seo_categories_description\n";
            }

            if (!empty($seo_categories_keywords)) {
              $embedding_data .= "Category SEO Keywords: $seo_categories_keywords\n";
            }

            $embeddedDocuments = NewVector::createEmbedding(null, $embedding_data);

            $embeddings = [];

            foreach ($embeddedDocuments as $embeddedDocument) {
              if (is_array($embeddedDocument->embedding)) {
                $embeddings[] = $embeddedDocument->embedding;
              }
            }

            if (!empty($embeddings)) {
              $flattened_embedding = $embeddings[0];
              $new_embedding_literal = json_encode($flattened_embedding, JSON_THROW_ON_ERROR);

              $sql_data_array = [
                'content' => $embedding_data,
                'type' => 'category',
                'sourcetype' => 'manual',
                'sourcename' => 'manual',
                'date_modified' => 'now()',
              ];

              $sql_data_array['vec_embedding'] = $new_embedding_literal;

              $this->app->db->save('categories_embedding', $sql_data_array, $update_sql_data);
            }
          }
        }
      }
    }
  }
}