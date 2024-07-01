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

    $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Categories/seo_chat_gpt');
  }

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
          if (isset($_POST['option_gpt_seo_title'])) {
            $question = $this->app->getDef('text_seo_page_title_question', ['category_name' => $categories_name]);

            $seo_product_title = $translate_language . ' ' . $language_name . ' : ' . $question;
            $seo_product_title = Gpt::getGptResponse($seo_product_title);

            if ($seo_product_title !== false) {
              $sql_data_array = [
                'categories_head_title_tag' => strip_tags($seo_product_title) ?? '',
              ];

              $this->app->db->save('categories_description', $sql_data_array, $update_sql_data);
            }
          }
//-------------------
// Seo description
//-------------------
          if (isset($_POST['option_gpt_seo_title'])) {
            $question_summary_description = $this->app->getDef('text_seo_page_summary_description_question', ['category_name' => $categories_name]);

            $seo_product_description = $translate_language . ' ' . $language_name . ' : ' . $question_summary_description;
            $seo_product_description = Gpt::getGptResponse($seo_product_description);

            if ($seo_product_description !== false) {
              $sql_data_array = [
                'categories_head_desc_tag' => strip_tags($seo_product_description) ?? '',
              ];

              $this->app->db->save('categories_description', $sql_data_array, $update_sql_data);
            }
          }
//-------------------
// Seo keywords
//-------------------
          if (isset($_POST['option_gpt_seo_keywords'])) {
            $question_keywords = $this->app->getDef('text_seo_page_keywords_question', ['category_name' => $categories_name]);

            $seo_product_keywords = $translate_language . ' ' . $language_name . ' : ' . $question_keywords;
            $seo_product_keywords = Gpt::getGptResponse($seo_product_keywords);

            if ($seo_product_keywords !== false) {
              $sql_data_array = [
                'categories_head_keywords_tag' => strip_tags($seo_product_keywords) ?? '',
              ];

              $this->app->db->save('categories_description', $sql_data_array, $update_sql_data);
            }
          }
        }
//-------------------
//image
//-------------------
/*
        if (isset($_POST['option_gpt_create_image'])) {
          $Qcategories = $this->app->db->prepare('select categories_name,
                                                           language_id
                                                    from :table_categories_description
                                                    where categories_id = :categories_id
                                                    and language_id = 1
                                                  ');
          $Qcategories->bindInt(':categories_id', $Qcheck->valueInt('categories_id'));
          $Qcategories->execute();

          $image = Gpt::createImageChatGpt($Qcategories->value('categories_name'), 'categories', '256x256');

          if (!empty($image) || $image !== false) {
            $sql_data_array = [
              'categories_image' => $image ?? '',
            ];

            $update_sql_data = ['categories_id' => $Qcheck->valueInt('categories_id')];

            $this->app->db->save('categories', $sql_data_array, $update_sql_data);
          }
        }
*/
      }
    }
  }
}