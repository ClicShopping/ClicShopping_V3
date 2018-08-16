<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  namespace ClicShopping\Apps\Catalog\Products\Module\HeaderTags;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\Products\Products as ProductsApp;

  class ProductsNews extends \ClicShopping\OM\Modules\HeaderTagsAbstract {

    protected $lang;
    protected $app;
    protected $group;

    protected function init() {
      if (!Registry::exists('Products')) {
        Registry::set('Products', new ProductsApp());
      }

      $this->app = Registry::get('Products');
      $this->lang = Registry::get('Language');
      $this->group = 'header_tags'; // could be header_tags or footer_scripts

      $this->app->loadDefinitions('Module/HeaderTags/products_news');

      $this->title = $this->app->getDef('module_header_tags_products_news_title');
      $this->description = $this->app->getDef('module_header_tags_products_news_description');

      if ( defined('MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_NEWS_STATUS') ) {
        $this->sort_order = (int)MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_NEWS_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_NEWS_STATUS == 'True');
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function getOutput() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!defined('CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS') || CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['products']) && isset($_GET['ProductsNew'])) {
        $Qsubmit = $this->app->D->prepare('select submit_id,
                                                  language_id,
                                                  submit_defaut_language_title,
                                                  submit_defaut_language_keywords,
                                                  submit_defaut_language_description,
                                                  submit_language_products_new_title,
                                                  submit_language_products_new_keywords,
                                                  submit_language_products_new_description
                                          from :table_submit_description
                                          where submit_id = 1
                                          and language_id = :language_id
                                        ');
        $Qsubmit->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId() );
        $Qsubmit->execute();

        $tags_array = [];

        if (empty($Qsubmit->value('submit_language_products_new_title'))) {
          $tags_array['title']= HTML::sanitize($Qsubmit->value('submit_defaut_language_title'));
        } else {
          $tags_array['title']= HTML::sanitize($Qsubmit->value('submit_language_products_new_title'));
        }

        if (empty($Qsubmit->value('submit_language_products_new_description'))) {
          $tags_array['desc']= HTML::sanitize($Qsubmit->value('submit_defaut_language_description'));
        } else {
          $tags_array['desc']= HTML::sanitize($Qsubmit->value('submit_language_products_new_description'));;
        }

        if (empty($Qsubmit->value('submit_language_products_new_keywords'))) {
          $tags_array['keywords']= HTML::sanitize($Qsubmit->value('submit_defaut_language_keywords'));
        } else {
          $tags_array['keywords']= HTML::sanitize($Qsubmit->value('submit_language_products_new_keywords'));;
        }

        $tags_array['title']= HTML::sanitize($Qsubmit->value('submit_defaut_language_title'));
        $tags_array['desc']= HTML::sanitize($Qsubmit->value('submit_defaut_language_description'));
        $tags_array['keywords']= HTML::sanitize($Qsubmit->value('submit_defaut_language_keywords'));

        $title = $CLICSHOPPING_Template->setTitle($tags_array['title'] . ', ' . $CLICSHOPPING_Template->getTitle());
        $description = $CLICSHOPPING_Template->setDescription($tags_array['desc'] . ', ' . $CLICSHOPPING_Template->getDescription());
        $keywords = $CLICSHOPPING_Template->setKeywords($tags_array['keywords'] . ', ' . $CLICSHOPPING_Template->getKeywords());
        $new_keywords = $CLICSHOPPING_Template->setNewsKeywords($tags_array['keywords'] . ', ' . $CLICSHOPPING_Template->getKeywords());

        $output =
<<<EOD
{$title}
{$description}
{$keywords}
{$new_keywords}
EOD;
        }

      return $output;
    }

    public function Install() {
      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want install this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_NEWS_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want install this module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );


      $this->app->db->save('configuration', [
          'configuration_title' => 'Display sort order',
          'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_NEWS_SORT_ORDER',
          'configuration_value' => '162',
          'configuration_description' => 'Display sort order (The lower is displayd in first)',
          'configuration_group_id' => '6',
          'sort_order' => '215',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function keys() {
      return ['MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_NEWS_STATUS',
              'MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_NEWS_SORT_ORDER'
             ];
    }
  }
