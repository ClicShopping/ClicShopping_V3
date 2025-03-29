<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Module\Hooks\ClicShoppingAdmin\Products;

use ClicShopping\OM\Registry;
use ClicShopping\OM\HTML;

use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Gpt;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\NewVector;
use ClicShopping\Sites\Common\HTMLOverrideCommon;

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
    $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/seo_chat_gpt');
    $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/rag');
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

    if (isset($_GET['Update'], $_GET['Products'])) {
      if (isset($_GET['pID'])){
        $pID = HTML::sanitize($_GET['pID']);

        $Qcategories = $this->app->db->prepare('select id
                                               from :table_products_embedding
                                               where products_id = :products_id
                                              ');
        $Qcategories->bindInt(':products_id',$pID);
        $Qcategories->execute();

        $insert_embedding = false;
        if ($Qcategories->fetch() === false) {
          $insert_embedding = true;
        }

        $Qproducts = $this->app->db->prepare('select p.products_id,
                                                     p.products_model,
                                                     p.manufacturers_id,
                                                     p.products_ean,
                                                     p.products_sku,
                                                     p.products_date_added,
                                                     p.products_status,
                                                     p.products_ordered,
                                                     p.products_quantity,                                                    
                                                     p.products_quantity_alert,
                                                     p.products_discountinued, 
                                                     pd.products_name,
                                                     pd.products_description,
                                                     pd.products_head_title_tag,
                                                     pd.products_head_desc_tag,
                                                     pd.products_head_keywords_tag,
                                                     pd.products_head_tag,
                                                     pd.products_description_summary,
                                                     pd.language_id
                                                from :table_products p,
                                                     :table_products_description pd
                                                where p.products_id = :products_id
                                                and p.products_id = pd.products_id
                                              ');
        $Qproducts->bindInt(':products_id', $pID);
        $Qproducts->execute();

        $products_array = $Qproducts->fetchAll();

        $Qcategories = $this->app->db->get('products_to_categories', ['categories_id'], ['products_id' => $pID]);

        if (is_array($products_array)) {
          foreach ($products_array as $item) {
            $products_name = $item['products_name'];
            $products_model = $item['products_model'];
            $products_ean = $item['products_ean'];
            $products_sku = $item['products_sku'];
            $products_date_added = $item['products_date_added'];
            $products_status = $item['products_status'];
            $products_ordered = $item['products_ordered'];
            $products_quantity = $item['products_quantity']; //product stock
            $products_stock_reorder_level = (int)STOCK_REORDER_LEVEL; //alert stock  fixfor all  products
            $products_quantity_alert = $item['products_quantity_alert']; // alert stock fix
            $products_discountinued = $item['products_discountinued']; // alert stock dynamic
            $manufacturer_name =  HTML::sanitize($_POST['manufacturers_name']);
            $products_description = $item['products_description'];
            $seo_product_title = $item['products_head_title_tag'];
            $seo_product_description = $item['products_head_desc_tag'];
            $seo_product_keywords = $item['products_head_keywords_tag'];
            $seo_product_tag = $item['products_head_tag'];
            $products_description_summary = $item['products_head_tag'];

            $update_sql_data = [
              'language_id' => $item['language_id'],
              'products_id' => $item['products_id']
            ];

            $Qcategories = $this->app->db->get('categories_description', 'categories_name', ['categories_id' => $Qcategories->valueInt('categories_id'), 'language_id' => $item['language_id']]);
            $categories_name = $Qcategories->value('categories_name');

  //********************
  // add embedding
  //********************
            $embedding_data = $this->app->getDef('text_product_name') . ': ' . HtmlOverrideCommon::cleanHtmlForEmbedding($products_name) . '\n';

            if (!empty($products_model)) {
              $embedding_data .= $this->app->getDef('text_product_model') . ': ' . HtmlOverrideCommon::cleanHtmlForEmbedding($products_model) . '\n';
            }

            if (!empty($categories_name)) {
              $embedding_data .= $this->app->getDef('text_categories_name') . ': ' . HtmlOverrideCommon::cleanHtmlForEmbedding($categories_name) . '\n';
            }

            if (!empty($manufacturer_name)) {
              $embedding_data .= $this->app->getDef('text_product_brand_name') . ': ' . HtmlOverrideCommon::cleanHtmlForEmbedding($manufacturer_name) . '\n';
            }

            if (!empty($products_ean)) {
              $embedding_data .= $this->app->getDef('text_product_ean') . ': ' . HtmlOverrideCommon::cleanHtmlForEmbedding($products_ean) . '\n';
            }

            if (!empty($products_sku)) {
              $embedding_data .= $this->app->getDef('text_product_sku') . ': ' . HtmlOverrideCommon::cleanHtmlForEmbedding($products_sku) . '\n';
            }

            if (!empty($products_date_added)) {
              $embedding_data .= $this->app->getDef('text_product_date_added') . ': ' . HtmlOverrideCommon::cleanHtmlForEmbedding($products_date_added) . '\n';
            }

            if (!empty($products_status)) {
              if ($products_status === 1) {
                $products_status = $this->app->getDef('text_product_enable');
              } else {
                $products_status = $this->app->getDef('text_product_disable');
              }

              $embedding_data .=  $this->app->getDef('text_product_status') . ': ' . $products_status . '\n';
            }

            if (!empty($products_ordered)) {
              $embedding_data .= $this->app->getDef('text_product_ordered') . ': ' . HtmlOverrideCommon::cleanHtmlForEmbedding($products_ordered) . '\n';
            }

            if (!empty($products_stock_reorder_level)) {
              $embedding_data .= $this->app->getDef('text_product_stock_reorder') . ': ' . HtmlOverrideCommon::cleanHtmlForEmbedding($products_stock_reorder_level) . '\n';
            }

            if (!empty($products_quantity)) {
              $embedding_data .= $this->app->getDef('text_product_stock') . ': ' . HtmlOverrideCommon::cleanHtmlForEmbedding($products_quantity) . '\n';
            }

            if (!empty($products_quantity_alert)) {
              $embedding_data .= $this->app->getDef('text_product_stock_alert') . ': ' . HtmlOverrideCommon::cleanHtmlForEmbedding($products_quantity_alert) . '\n';
            }

            if (!empty($products_discountinued)) {
              $embedding_data .= $this->app->getDef('text_product_stock_dynamic_alert') . ': ' . HtmlOverrideCommon::cleanHtmlForEmbedding($products_discountinued) . '\n';
            }

            if (!empty($products_description)) {
              $embedding_data .= $this->app->getDef('text_product_description') . ': ' . HtmlOverrideCommon::cleanHtmlForEmbedding($products_description) . '\n';
            }

            if (!empty($products_description_summary)) {
              $embedding_data .= $this->app->getDef('text_product_description_summary') . ': ' . HtmlOverrideCommon::cleanHtmlForEmbedding($products_description_summary) . '\n';
            }

            if (!empty($seo_product_title)) {
              $embedding_data .= $this->app->getDef('text_product_seo_title') . ': ' . HtmlOverrideCommon::cleanHtmlForSEO($seo_product_title) . '\n';
            }

            if (!empty($seo_product_description)) {
              $embedding_data .= $this->app->getDef('text_product_seo_description') . ': ' . HtmlOverrideCommon::cleanHtmlForSEO($seo_product_description) . '\n';
            }

            if (!empty($seo_product_keywords)) {
              $embedding_data .= $this->app->getDef('text_product_seo_keywords') . ': ' . HtmlOverrideCommon::cleanHtmlForSEO($seo_product_keywords) . '\n';
            }

            if (!empty($seo_product_tag)) {
              $embedding_data .= $this->app->getDef('text_product_seo_tag') . ': ' . HtmlOverrideCommon::cleanHtmlForSEO($seo_product_tag) . '\n';
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

              $sql_data_array_embedding = [
                'content' => $embedding_data,
                'type' => 'products',
                'sourcetype' => 'manual',
                'sourcename' => 'manual',
                'date_modified' => 'now()',
              ];

              $sql_data_array_embedding['vec_embedding'] = $new_embedding_literal;

             if ($insert_embedding === true) {
                $sql_data_array_embedding['products_id'] = $item['products_id'];
                $sql_data_array_embedding['language_id'] =  $item['language_id'];

                $this->app->db->save('products_embedding', $sql_data_array_embedding);
              } else {
                $this->app->db->save('products_embedding', $sql_data_array_embedding, $update_sql_data);
              }
            }
          }
        }
      }
    }
  }
}