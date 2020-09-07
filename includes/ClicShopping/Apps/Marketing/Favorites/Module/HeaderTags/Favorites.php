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

  namespace ClicShopping\Apps\Marketing\Favorites\Module\HeaderTags;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\Favorites\Favorites as FavoritesApp;

  use ClicShopping\Apps\Marketing\SEO\Classes\Shop\SeoShop as SeoShopFavorites;

  class Favorites extends \ClicShopping\OM\Modules\HeaderTagsAbstract
  {
    protected $lang;
    protected $app;
    protected $template;

    protected function init()
    {
      if (!Registry::exists('Favorites')) {
        Registry::set('Favorites', new FavoritesApp());
      }

      $this->group = 'header_tags'; // could be header_tags or footer_scripts
      $this->app = Registry::get('Favorites');

      $this->app->loadDefinitions('Module/HeaderTags/products_favorites');

      $this->title = $this->app->getDef('module_header_tags_products_favorites_title');
      $this->description = $this->app->getDef('module_header_tags_products_favorites_description');

      if (defined('MODULE_HEADER_TAGS_PRODUCT_FAVORITES_STATUS')) {
        $this->sort_order = (int)MODULE_HEADER_TAGS_PRODUCT_FAVORITES_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_PRODUCT_FAVORITES_STATUS == 'True');
      }
    }

    public function isEnabled()
    {
      return $this->enabled;
    }

    public function getOutput()
    {
      if (isset($_GET['Products']) && isset($_GET['Favorites'])) {
        $this->template = Registry::get('Template');

        if (!Registry::exists('SeoShopFavorites')) {
          Registry::set('SeoShopFavorites', new SeoShopFavorites());
        }

        $CLICSHOPPING_SEOShop = Registry::get('SeoShopFavorites');

        $title = $CLICSHOPPING_SEOShop->getSeoFavoritesTitle();
        $description = $CLICSHOPPING_SEOShop->getSeoFavoritesDescription();
        $keywords = $CLICSHOPPING_SEOShop->getSeoFavoritesKeywords();

        $title = $this->template->setTitle($title) . ' ' . $this->template->getTitle();
        $description = $this->template->setDescription($description) . ' ' . $this->template->getDescription();
        $keywords = $this->template->setKeywords($keywords) . ', ' . $this->template->getKeywords();

        $output =
          <<<EOD
    <title>{$title}</title>
    <meta name="description" content="{$description}" />
    <meta name="keywords"  content="{$keywords}" />
    <meta name="news_keywords" content="{$keywords}" />
EOD;

        return $output;
      }
    }

    public function Install()
    {
      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want to install this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_FAVORITES_STATUS',
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
          'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_FAVORITES_SORT_ORDER',
          'configuration_value' => '165',
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
      return ['MODULE_HEADER_TAGS_PRODUCT_FAVORITES_STATUS',
        'MODULE_HEADER_TAGS_PRODUCT_FAVORITES_SORT_ORDER'
      ];
    }
  }
