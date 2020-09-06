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

  namespace ClicShopping\Apps\Marketing\SEO\Module\HeaderTags;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTTP;

  use ClicShopping\Apps\Marketing\SEO\SEO as SEOApp;

  use ClicShopping\Apps\Marketing\SEO\Classes\Shop\SeoShop as SeoShopIindex;

  class Index extends \ClicShopping\OM\Modules\HeaderTagsAbstract
  {
    protected $lang;
    protected $app;
    protected $template;

    protected function init()
    {
      if (!Registry::exists('SEO')) {
        Registry::set('SEO', new SEOApp());
      }

      $this->app = Registry::get('SEO');
      $this->lang = Registry::get('Language');
      $this->group = 'header_tags'; // could be header_tags or footer_scripts

      $this->app->loadDefinitions('Module/header_tag/index');

      $this->title = $this->app->getDef('module_header_tags_index_title');
      $this->description = $this->app->getDef('module_header_tags_index_description');

      if (defined('MODULE_HEADER_TAGS_INDEX_STATUS')) {
        $this->sort_order = (int)MODULE_HEADER_TAGS_INDEX_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_INDEX_STATUS == 'True');
      }
    }

    public function isEnabled()
    {
      return $this->enabled;
    }

    public function getOutput()
    {
      $this->template = Registry::get('Template');

      $output = '';

      if (HTTP::getUri() === CLICSHOPPING::getConfig('http_path', 'Shop') || HTTP::getUri() === CLICSHOPPING::getConfig('http_path', 'Shop') . 'index.php') {
        if (!Registry::exists('SeoShopIindex')) {
          Registry::set('SeoShopIindex', new SeoShopIindex());
        }

         $CLICSHOPPING_SEOShop = Registry::get('SeoShopIindex');

         $title = $CLICSHOPPING_SEOShop->getSeoIndexTitle();
         $description = $CLICSHOPPING_SEOShop->getSeoIndexDescription();
         $keywords = $CLICSHOPPING_SEOShop->getSeoIndexKeywords();

         $title = $this->template->setTitle($title) . ' ' . $this->template->getTitle();
         $description = $this->template->setDescription($description) . ', ' . $this->template->getDescription();
         $keywords = $this->template->setKeywords($keywords) . ', ' . $this->template->getKeywords();

         $output =
         <<<EOD
    <title>{$title}</title>
    <meta name="description" content="{$description}" />
    <meta name="keywords" content="{$keywords}" />
    <meta name="news_keywords" content="{$keywords}" />
EOD;
      } elseif (isset($_GET['Account'])) {
        if (!Registry::exists('SeoShopIindex')) {
          Registry::set('SeoShopIindex', new SeoShopIindex());
        }

        $CLICSHOPPING_SEOShop = Registry::get('SeoShopIindex');

        $title = $CLICSHOPPING_SEOShop->getSeoIndexTitle();
        $description = $CLICSHOPPING_SEOShop->getSeoIndexDescription();
        $keywords = $CLICSHOPPING_SEOShop->getSeoIndexKeywords();

        $title = $this->template->setTitle($title) . ', ' . $this->template->getTitle();
        $description = $this->template->setDescription($description) . ', ' . $this->template->getDescription();
        $keywords = $this->template->setKeywords($keywords) . ', ' . $this->template->getKeywords();

        $output =
<<<EOD
    <title>{$title}</title>
    <meta name="description" content="{$description}" />
    <meta name="keywords" content="{$keywords}" />
    <meta name="news_keywords" content="{$keywords}" />
EOD;
      }

      return $output;
    }

    public function Install()
    {
      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want to install this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_INDEX_STATUS',
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
          'configuration_key' => 'MODULE_HEADER_TAGS_INDEX_SORT_ORDER',
          'configuration_value' => '161',
          'configuration_description' => 'Display sort order (The lower is displayd in first)',
          'configuration_group_id' => '6',
          'sort_order' => '215',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function remove()
    {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys()
    {
      return ['MODULE_HEADER_TAGS_INDEX_STATUS',
        'MODULE_HEADER_TAGS_INDEX_SORT_ORDER'
      ];
    }
  }
