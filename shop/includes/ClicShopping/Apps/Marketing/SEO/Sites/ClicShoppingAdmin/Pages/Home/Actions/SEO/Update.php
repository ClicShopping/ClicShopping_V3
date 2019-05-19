<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  namespace ClicShopping\Apps\Marketing\SEO\Sites\ClicShoppingAdmin\Pages\Home\Actions\SEO;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_SEO = Registry::get('SEO');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Language = Registry::get('Language');

      $languages = $CLICSHOPPING_Language->getLanguages();

// Language
      for ($i = 0, $n = count($languages); $i < $n; $i++) {

        $submit_defaut_language_title = HTML::sanitize($_POST['submit_defaut_language_title_' . $languages[$i]['id']]);
        $submit_defaut_language_keywords = HTML::sanitize($_POST['submit_defaut_language_keywords_' . $languages[$i]['id']]);
        $submit_defaut_language_description = HTML::sanitize($_POST['submit_defaut_language_description_' . $languages[$i]['id']]);
        $submit_defaut_language_footer = HTML::sanitize($_POST['submit_defaut_language_footer_' . $languages[$i]['id']]);
        $submit_language_products_info_title = HTML::sanitize($_POST['submit_language_products_info_title_' . $languages[$i]['id']]);
        $submit_language_products_info_keywords = HTML::sanitize($_POST['submit_language_products_info_keywords_' . $languages[$i]['id']]);
        $submit_language_products_info_description = HTML::sanitize($_POST['submit_language_products_info_description_' . $languages[$i]['id']]);
        $submit_language_products_new_title = HTML::sanitize($_POST['submit_language_products_new_title_' . $languages[$i]['id']]);
        $submit_language_products_new_keywords = HTML::sanitize($_POST['submit_language_products_new_keywords_' . $languages[$i]['id']]);
        $submit_language_products_new_description = HTML::sanitize($_POST['submit_language_products_new_description_' . $languages[$i]['id']]);
        $submit_language_special_title = HTML::sanitize($_POST['submit_language_special_title_' . $languages[$i]['id']]);
        $submit_language_special_keywords = HTML::sanitize($_POST['submit_language_special_keywords_' . $languages[$i]['id']]);
        $submit_language_special_description = HTML::sanitize($_POST['submit_language_special_description_' . $languages[$i]['id']]);
        $submit_language_reviews_title = HTML::sanitize($_POST['submit_language_reviews_title_' . $languages[$i]['id']]);
        $submit_language_reviews_keywords = HTML::sanitize($_POST['submit_language_reviews_keywords_' . $languages[$i]['id']]);
        $submit_language_reviews_description = HTML::sanitize($_POST['submit_language_reviews_description_' . $languages[$i]['id']]);

        $sql_data_array_pages_description = ['submit_defaut_language_title' => $submit_defaut_language_title,
          'submit_defaut_language_keywords' => $submit_defaut_language_keywords,
          'submit_defaut_language_description' => $submit_defaut_language_description,
          'submit_defaut_language_footer' => $submit_defaut_language_footer,
          'submit_language_products_info_title' => $submit_language_products_info_title,
          'submit_language_products_info_keywords' => $submit_language_products_info_keywords,
          'submit_language_products_info_description' => $submit_language_products_info_description,
          'submit_language_products_new_title' => $submit_language_products_new_title,
          'submit_language_products_new_keywords' => $submit_language_products_new_keywords,
          'submit_language_products_new_description' => $submit_language_products_new_description,
          'submit_language_special_title' => $submit_language_special_title,
          'submit_language_special_keywords' => $submit_language_special_keywords,
          'submit_language_special_description' => $submit_language_special_description,
          'submit_language_reviews_title' => $submit_language_reviews_title,
          'submit_language_reviews_keywords' => $submit_language_reviews_keywords,
          'submit_language_reviews_description' => $submit_language_reviews_description
        ];

        $Qcheck = $CLICSHOPPING_SEO->db->prepare('select count(*) as countrecords
                                           from :table_submit_description
                                           where submit_id = 1
                                           and language_id = :language_id
                                          ');
        $Qcheck->bindInt(':language_id', (int)$languages[$i]['id']);
        $Qcheck->execute();

        if ($Qcheck->rowCount() >= 1) {

          $CLICSHOPPING_SEO->db->save('submit_description', $sql_data_array_pages_description, ['submit_id' => 1,
              'language_id' => (int)$languages[$i]['id']
            ]
          );

          $CLICSHOPPING_Hooks->call('SEO', 'Update');
        }
      }

      $CLICSHOPPING_SEO->redirect('SEO&SEO');
    }
  }