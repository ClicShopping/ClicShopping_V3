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

use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;
use ClicShopping\Apps\Configuration\ChatGpt\Classes\ClicShoppingAdmin\Gpt;

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

  public function execute()
  {
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');
/*
    if (!\defined('CLICSHOPPING_APP_CHATGPT_CH_STATUS') || CLICSHOPPING_APP_CHATGPT_CH_STATUS == 'False') {
      return false;
    }
*/
    if (isset($_GET['Insert'], $_GET['Products'])) {
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

          $products_name_array = ['products_name' => $product_name];
//-------------------
// products description
//-------------------
          if (isset($_POST['option_gpt_description'])) {
            $question_description = $this->app->getDef('text_question_description', $products_name_array);
            $technical_question = $this->app->getDef('text_technical_question', $products_name_array);

            $products_description = $translate_language . ' ' . $language_name . '. ' . $question_description . ' ' . $technical_question;
            $products_description = Gpt::getGptResponse($products_description);

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
          if (isset($_POST['option_gpt_summary_description'])) {
            $question_summary_description = $this->app->getDef('text_seo_page_summary_description_question', $products_name_array);

            $summary_description = $translate_language . ' ' . $language_name . ' : ' . $question_summary_description;
            $summary_description = Gpt::getGptResponse($summary_description);

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
          if (isset($_POST['option_gpt_seo_title'])) {
            $question = $this->app->getDef('text_seo_page_title_question', $products_name_array);

            $seo_product_title = $translate_language . ' ' . $language_name . ' : ' . $question;
            $seo_product_title = Gpt::getGptResponse($seo_product_title);

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
          if (isset($_POST['option_gpt_seo_description'])) {
            $seo_product_description = $translate_language . ' ' . $language_name . ' : ' . $question_summary_description;
            $seo_product_description = Gpt::getGptResponse($seo_product_description);

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
          if (isset($_POST['option_gpt_seo_keywords'])) {
            $question_keywords = $this->app->getDef('text_seo_page_keywords_question', $products_name_array);

            $seo_product_keywords = $translate_language . ' ' . $language_name . ' : ' . $question_keywords;
            $seo_product_keywords = Gpt::getGptResponse($seo_product_keywords);

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
          if (isset($_POST['option_gpt_seo_tags'])) {
            $question_tag = $this->app->getDef('text_seo_page_tag_question', $products_name_array);

            $seo_product_tag = $translate_language . ' ' . $language_name . ' : ' . $question_tag;
            $seo_product_tag = Gpt::getGptResponse($seo_product_tag);

            if ($seo_product_tag !== false) {
              $sql_data_array = [
                'products_head_tag' => $seo_product_tag ?? '',
              ];

              $this->app->db->save('products_description', $sql_data_array, $update_sql_data);
            }
          }
        }
//-------------------
//image
//-------------------
/*
        if (isset($_POST['option_gpt_create_image'])) {
          $Qproducts = $this->app->db->prepare('select products_name,
                                                         language_id
                                                  from :table_products_description
                                                  where products_id = :products_id
                                                  and language_id = 1
                                                ');
          $Qproducts->bindInt(':products_id', $Qcheck->valueInt('products_id'));
          $Qproducts->execute();

          $update_sql_data = [
            'products_id' => $Qcheck->valueInt('products_id')
          ];

          $products_image = Gpt::createImageChatGpt($Qproducts->value('products_name'), 'products', '256x256', true, true);

          if (!empty($products_image) || $products_image !== false) {
            $sql_data_products_image = [
              'products_image' => $products_image ?? '',
              'products_image_small' => $products_image ?? ''
            ];

            $this->app->db->save('products', $sql_data_products_image, $update_sql_data);
          }

//zoom
          $products_image_zoom = Gpt::createImageChatGpt($Qproducts->value('products_name'), 'products', '512x512', true);

          if (!empty($products_image_zoom) || $products_image_zoom !== false) {
            $sql_data_array_products_image_zoom = [
              'products_image_zoom' => $products_image_zoom ?? '',
            ];

            $this->app->db->save('products', $sql_data_array_products_image_zoom, $update_sql_data);

            $sql_array = [
              'products_id' => $Qcheck->valueInt('products_id'),
              'image' => $products_image_zoom ?? '',
              'htmlcontent' => '',
              'sort_order' => 2
            ];

            $this->app->db->save('products_images', $sql_array);
          }

// medium
          $products_image_medium = Gpt::createImageChatGpt($Qproducts->value('products_name'), 'products', '512x512', true);

          if (!empty($products_image_medium) || $products_image_medium !== false) {
            $sql_data_array_products_image_medium = [
              'products_image_medium' => $products_image_medium ?? '',
            ];

            $this->app->db->save('products', $sql_data_array_products_image_medium, $update_sql_data);

            $sql_array = [
              'products_id' => $Qcheck->valueInt('products_id'),
              'image' => $products_image_medium ?? '',
              'htmlcontent' => '',
              'sort_order' => 2
            ];

            $this->app->db->save('products_images', $sql_array);
          }
        }
*/
      }
    }
  }
}