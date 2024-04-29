<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Module\Hooks\ClicShoppingAdmin\Manufacturers;

use ClicShopping\Apps\Configuration\ChatGpt\ChatGpt as ChatGptApp;
use ClicShopping\OM\Registry;

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
/*
    if (Gpt::checkGptStatus() === false) {
      return false;
    }
*/
    if (isset($_GET['Insert'], $_GET['Manufacturers'])) {
      $question = $this->app->getDef('text_seo_page_title_question');
      $question_keywords = $this->app->getDef('text_seo_page_keywords_question');
      $translate_language = $this->app->getDef('text_seo_page_translate_language');
      $question_summary_description = $this->app->getDef('text_seo_page_summary_description_question');

      $Qcheck = $this->app->db->prepare('select manufacturers_id,
                                                  manufacturers_name
                                            from :table_manufacturers
                                            order by manufacturers_id desc
                                            limit 1
                                          ');
      $Qcheck->execute();

      $manufacturers_name = $Qcheck->value('manufacturers_name');

      if ($Qcheck->valueInt('manufacturers_id') !== null) {
        $Qmanufacturers = $this->app->db->prepare('select manufacturers_id,
                                                            languages_id
                                                from :table_manufacturers_info
                                                where manufacturers_id = :manufacturers_id
                                              ');
        $Qmanufacturers->bindInt(':manufacturers_id', $Qcheck->valueInt('manufacturers_id'));
        $Qmanufacturers->execute();

        $manufacturers_array = $Qmanufacturers->fetchAll();

        foreach ($manufacturers_array as $item) {
          $language_name = $CLICSHOPPING_Language->getLanguagesName($item['languages_id']);

          $update_sql_data = [
            'languages_id' => $item['languages_id'],
            'manufacturers_id' => $item['manufacturers_id']
          ];

//-------------------
// products description
//-------------------
          if (isset($_POST['option_gpt_description'])) {
            $manufacturers_description = $translate_language . ' ' . $language_name . ' : ' . $question_summary_description . ' ' . $manufacturers_name;
            $manufacturers_description = Gpt::getGptResponse($manufacturers_description);

            if ($manufacturers_description !== false) {
              $sql_data_array = [
                'manufacturer_description' => $manufacturers_description ?? '',
              ];

              $this->app->db->save('manufacturers_info', $sql_data_array, $update_sql_data);
            }
          }

////-------------------
// Seo Title
//-------------------
          if (isset($_POST['option_gpt_seo_title'])) {
            $seo_product_title = $translate_language . ' ' . $language_name . ' : ' . $question . ' ' . $manufacturers_name;
            $seo_product_title = Gpt::getGptResponse($seo_product_title);

            if ($seo_product_title !== false) {
              $sql_data_array = [
                'manufacturer_seo_title' => $seo_product_title ?? '',
              ];

              $this->app->db->save('manufacturers_info', $sql_data_array, $update_sql_data);
            }
          }
//-------------------
// Seo description
//-------------------
          if (isset($_POST['option_gpt_seo_title'])) {
            $seo_product_description = $translate_language . ' ' . $language_name . ' : ' . $question_summary_description . ' ' . $manufacturers_name;
            $seo_product_description = Gpt::getGptResponse($seo_product_description);

            if ($seo_product_description !== false) {
              $sql_data_array = [
                'manufacturer_seo_description' => $seo_product_description ?? '',
              ];

              $this->app->db->save('manufacturers_info', $sql_data_array, $update_sql_data);
            }
          }
//-------------------
// Seo keywords
//-------------------
          if (isset($_POST['option_gpt_seo_keywords'])) {
            $seo_product_keywords = $translate_language . ' ' . $language_name . ' : ' . $question_keywords . ' ' . $manufacturers_name;
            $seo_product_keywords = Gpt::getGptResponse($seo_product_keywords);

            if ($seo_product_keywords !== false) {
              $sql_data_array = [
                'manufacturer_seo_keyword' => $seo_product_keywords ?? '',
              ];

              $this->app->db->save('manufacturers_info', $sql_data_array, $update_sql_data);
            }
          }
        }
      }
//-------------------
//image
//-------------------
/*
      if (isset($_POST['option_gpt_create_image'])) {
        $image = Gpt::createImageChatGpt($manufacturers_name, 'manufacturers');

        if (!empty($image) || $image !== false) {
          $sql_data_array = [
            'manufacturers_image' => $image ?? '',
          ];

          $update_sql_data = [
            'manufacturers_id' => $Qcheck->valueInt('manufacturers_id')
          ];

          $this->app->db->save('manufacturers', $sql_data_array, $update_sql_data);
        }
      }
*/
    }
  }
}