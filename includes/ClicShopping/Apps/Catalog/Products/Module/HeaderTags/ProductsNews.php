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

  namespace ClicShopping\Apps\Catalog\Products\Module\HeaderTags;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\Products\Products as ProductsApp;

  class ProductsNews extends \ClicShopping\OM\Modules\HeaderTagsAbstract
  {
    protected $lang;
    protected $app;
    public $group;
    public $title;
    public $description;
    public $sort_order = 0;
    public $enabled;

    protected function init()
    {
      if (!Registry::exists('Products')) {
        Registry::set('Products', new ProductsApp());
      }

      $this->app = Registry::get('Products');
      $this->lang = Registry::get('Language');
      $this->group = 'header_tags'; // could be header_tags or footer_scripts

      $this->app->loadDefinitions('Module/HeaderTags/products_news');

      $this->title = $this->app->getDef('module_header_tags_products_news_title');
      $this->description = $this->app->getDef('module_header_tags_products_news_description');

      if (defined('MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_NEWS_STATUS')) {
        $this->sort_order = (int)MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_NEWS_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_NEWS_STATUS == 'True');
      }
    }

    public function isEnabled()
    {
      return $this->enabled;
    }

    public function getOutput()
    {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!defined('CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS') || CLICSHOPPING_APP_CATALOG_PRODUCTS_PD_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Products']) && isset($_GET['ProductsNew'])) {
        $Qsubmit = $this->app->db->prepare('select submit_id,
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
        $Qsubmit->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
        $Qsubmit->execute();

        $store_name = HTML::sanitize(STORE_NAME);

        if (empty($Qsubmit->value('submit_language_products_new_title'))) {
          $title = HTML::sanitize($Qsubmit->value('submit_defaut_language_title')) . ', ' . $CLICSHOPPING_Template->getTitle();
        } else {
          $title = HTML::sanitize($Qsubmit->value('submit_language_products_new_title')) . ', ' . $CLICSHOPPING_Template->getTitle();
        }
        
        if (empty($Qsubmit->value('submit_language_products_new_description'))) {
          $description = HTML::sanitize($Qsubmit->value('submit_defaut_language_description')) . ', ' . $CLICSHOPPING_Template->getDescription() . ', ' . $store_name;
        } else {
          $description = HTML::sanitize($Qsubmit->value('submit_language_products_new_description')) . ', ' . $CLICSHOPPING_Template->getDescription() . ', ' . $store_name;
        }

        if (empty($Qsubmit->value('submit_language_products_new_keywords'))) {
          $keywords = HTML::sanitize($Qsubmit->value('submit_defaut_language_keywords')) . ', ' . $CLICSHOPPING_Template->getKeywords() . ', ' . $store_name;
        } else {
          $keywords = HTML::sanitize($Qsubmit->value('submit_language_products_new_keywords')) . ', ' . $CLICSHOPPING_Template->getKeywords() . ', ' . $store_name;
        }

        $output =
          <<<EOD
    <title>{$title}</title>
    <meta name="Description" content="{$description}" />
    <meta name="Keywords" content="{$keywords}" />
    <meta name="news_keywords" content="{$keywords}" />
EOD;

        return $output;
      }
    }

    public function Install()
    {
      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want to install this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_NEWS_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to install this module ?',
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

    public function keys()
    {
      return ['MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_NEWS_STATUS',
        'MODULE_HEADER_TAGS_PRODUCT_PRODUCTS_NEWS_SORT_ORDER'
      ];
    }
  }
