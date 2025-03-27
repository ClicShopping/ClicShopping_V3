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

use ClicShopping\OM\Registry;
use ClicShopping\OM\HTML;

use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Gpt;
use ClicShopping\Sites\Common\HTMLOverrideCommon;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\NewVector;

class Update implements \ClicShopping\OM\Modules\HooksInterface
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
  }

  /**
   * Executes the necessary processes based on the provided GET and POST parameters related to category handling.
   *
   * Checks if GPT functionality is enabled and processes category-related inputs to update database records
   * such as descriptions, SEO data (title, description, keywords),
   *
   * @return bool Returns false if GPT functionality is disabled or not applicable; otherwise, performs the operations without returning a value.
   */
  public function execute()
  {
    if (Gpt::checkGptStatus() === false) {
      return false;
    }

    if (CLICSHOPPING_APP_CHATGPT_CH_OPENAI_EMBEDDING == 'False') {
      return false;
    }

    if (isset($_GET['Update'], $_GET['Categories'])) {
      if (isset($_GET['cID'])){
        $cID = HTML::sanitize($_GET['cID']);

        $Qcategories = $this->app->db->prepare('select id
                                               from :table_categories_embedding
                                               where categories_id = :categories_id
                                              ');
        $Qcategories->bindInt(':categories_id',$cID);
        $Qcategories->execute();

        $insert_embedding = false;
        if ($Qcategories->fetch() === false) {
          $insert_embedding = true;
        }

        $Qcategories = $this->app->db->prepare('select categories_id,
                                                       categories_name,
                                                       categories_description,
                                                       categories_head_title_tag,
                                                       categories_head_desc_tag,
                                                       categories_head_keywords_tag,
                                                       language_id
                                             from :table_categories_description
                                             where categories_id = :categories_id
                                            ');
        $Qcategories->bindInt(':categories_id',$cID);
        $Qcategories->execute();

        $categories_array = $Qcategories->fetchAll();

        if (is_array($categories_array)) {
          foreach ($categories_array as $item) {
            $language_id = $item['language_id'];
            $categories_name = $item['categories_name'];
            $categories_description = $item['categories_description'];
            $seo_categories_title = $item['categories_head_title_tag'];
            $seo_categories_description = $item['categories_head_desc_tag'];
            $seo_categories_keywords = $item['categories_head_keywords_tag'];

            $update_sql_data = [
              'language_id' => $item['language_id'],
              'categories_id' => $item['categories_id']
            ];

//********************
// add embedding
//********************
            $embedding_data = 'Category Name: ' . HtmlOverrideCommon::cleanHtmlForEmbedding($categories_name) . '\n';

            if (!empty($categories_description)) {
              $embedding_data .= 'Category Description: ' . HtmlOverrideCommon::cleanHtmlForEmbedding($categories_description) . '\n';
            }

            if (!empty($seo_categories_title)) {
              $embedding_data .= 'Category SEO Title: ' .  HtmlOverrideCommon::cleanHtmlForSEO($seo_categories_title) . '\n';
            }

            if (!empty($seo_categories_description)) {
              $embedding_data .= 'Category SEO Description: ' .  HtmlOverrideCommon::cleanHtmlForSEO($seo_categories_description) . '\n';
            }

            if (!empty($seo_categories_keywords)) {
              $embedding_data .= 'Category SEO Keywords: ' .  HtmlOverrideCommon::cleanHtmlForSEO($seo_categories_keywords) . '\n';
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

              $sql_data_array_embedding= [
                'content' => $embedding_data,
                'type' => 'category',
                'sourcetype' => 'manual',
                'sourcename' => 'manual',
                'date_modified' => 'now()'
              ];

              $sql_data_array_embedding['vec_embedding'] = $new_embedding_literal;

              if ($insert_embedding === true) {
                $sql_data_array_embedding['categories_id'] = $item['categories_id'];
                $sql_data_array_embedding['language_id'] =  $item['language_id'];
                $this->app->db->save('categories_embedding', $sql_data_array_embedding);
              } else {
                $this->app->db->save('categories_embedding', $sql_data_array_embedding, $update_sql_data);
              }
            }
          }
        }
      }
    }
  }
}