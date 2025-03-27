<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Module\Hooks\ClicShoppingAdmin\PageManager;

use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\NewVector;
use ClicShopping\OM\Registry;
use ClicShopping\OM\HTML;

use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Gpt;
use ClicShopping\Sites\Common\HTMLOverrideCommon;

class Save implements \ClicShopping\OM\Modules\HooksInterface
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

    if (isset($_GET['Update'], $_GET['PageManager'])) {
      if (isset($_POST['pages_id'])) {
        $pages_id = HTML::sanitize($_POST['pages_id']);

        $QpageManager = $this->app->db->prepare('select id
                                               from :table_pages_manager_embedding
                                               where pages_id = :pages_id
                                              ');
        $QpageManager->bindInt(':pages_id',$pages_id);
        $QpageManager->execute();

        $insert_embedding = false;
        if ($QpageManager->fetch() === false) {
          $insert_embedding = true;
        }

        $QpageManager = $this->app->db->prepare('select pages_id,
                                                       pages_title,
                                                       pages_html_text,
                                                       page_manager_head_title_tag,
                                                       page_manager_head_desc_tag,
                                                       page_manager_head_keywords_tag,
                                                       language_id
                                                 from :table_pages_manager_description
                                                 where pages_id = :pages_id
                                                ');
        $QpageManager->bindInt(':pages_id',$pages_id);
        $QpageManager->execute();

        $page_manager_array = $QpageManager->fetchAll();

        if (is_array($page_manager_array)) {
          foreach ($page_manager_array as $item) {
            $page_manager_name = isset($item['pages_title']) ? HtmlOverrideCommon::cleanHtmlForEmbedding($item['pages_title']) : '';
            $page_manager_description = isset($item['pages_html_text']) ? HtmlOverrideCommon::cleanHtmlForEmbedding($item['pages_html_text']) : '';
            $seo_page_manager_title = isset($item['page_manager_head_title_tag']) ? HtmlOverrideCommon::cleanHtmlForSEO($item['page_manager_head_title_tag']) : '';
            $seo_page_manager_description = isset($item['page_manager_head_desc_tag']) ? HtmlOverrideCommon::cleanHtmlForSEO($item['page_manager_head_desc_tag']) : '';
            $seo_page_manager_keywords = isset($item['page_manager_head_keywords_tag']) ? HtmlOverrideCommon::cleanHtmlForSEO($item['page_manager_head_keywords_tag']) : '';

            $update_sql_data = [
              'language_id' => $item['language_id'],
              'pages_id' => $item['pages_id']
            ];

//********************
// add embedding
//********************
            $embedding_data = "Page Manager Name: $page_manager_name\n";

            if (!empty($page_manager_description)) {
              $embedding_data .= "Page Manager Description: $page_manager_description\n";
            }

            if (!empty($seo_page_manager_title)) {
              $embedding_data .= "Page Manager SEO Title: $seo_page_manager_title\n";
            }

            if (!empty($seo_page_manager_description)) {
              $embedding_data .= "Page Manager SEO Description: $seo_page_manager_description\n";
            }

            if (!empty($seo_page_manager_keywords)) {
              $embedding_data .= "Page Manager SEO Keywords: $seo_page_manager_keywords\n";
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
                'type' => 'Page Manager',
                'sourcetype' => 'manual',
                'sourcename' => 'manual',
                'date_modified' => 'now()'
              ];

              $sql_data_array_embedding['vec_embedding'] = $new_embedding_literal;

              if ($insert_embedding === true) {
                $sql_data_array_embedding['pages_id'] = $item['pages_id'];
                $sql_data_array_embedding['language_id'] =  $item['language_id'];
                $this->app->db->save('pages_manager_embedding', $sql_data_array_embedding);
              } else {
                $this->app->db->save('pages_manager_embedding', $sql_data_array_embedding, $update_sql_data);
              }
            }
          }
        }
      }
    }
  }
}