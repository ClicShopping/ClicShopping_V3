<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Communication\PageManager\Module\HeaderTags;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Communication\PageManager\PageManager as PageManagerApp;

  class RSS extends \ClicShopping\OM\Modules\HeaderTagsAbstract
  {
    protected mixed $lang;
    protected mixed $app;

    protected function init()
    {
      if (!Registry::exists('PageManager')) {
        Registry::set('PageManager', new PageManagerApp());
      }

      $this->app = Registry::get('PageManager');
      $this->lang = Registry::get('Language');
      $this->group = 'header_tags'; // could be header_tags or footer_scripts

      $this->app->loadDefinitions('Module/HeaderTags/rss');

      $this->title = $this->app->getDef('module_header_tags_rss_title');
      $this->description = $this->app->getDef('module_header_tags_rss_description');

      if (\defined('MODULE_HEADER_TAGS_RSS_STATUS')) {
        $this->sort_order = (int)MODULE_HEADER_TAGS_RSS_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_RSS_STATUS == 'True');
      }
    }

    public function isEnabled()
    {
      return $this->enabled;
    }

    public function getOutput()
    {
      $CLICSHOPPING_Template = Registry::get('Template');

      if (!\defined('CLICSHOPPING_APP_PAGE_MANAGER_PM_STATUS') || CLICSHOPPING_APP_PAGE_MANAGER_PM_STATUS == 'False') {
        return false;
      }

      $xml = $CLICSHOPPING_Template->addBlock('<link rel="alternate" type="application/rss+xml" title="' . HTML::outputProtected(STORE_NAME) . '" href="' . HTTP::getShopUrlDomain() . 'index.php?Info&RSS' . '">', $this->group);

      $output =
        <<<EOD
{$xml}
EOD;

      return $output;
    }

    public function Install()
    {
      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want to install this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_RSS_STATUS',
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
          'configuration_key' => 'MODULE_HEADER_TAGS_RSS_SORT_ORDER',
          'configuration_value' => '210',
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
      return ['MODULE_HEADER_TAGS_RSS_STATUS',
        'MODULE_HEADER_TAGS_RSS_SORT_ORDER'
      ];
    }
  }
