<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class ht_breadcrumb
  {
    public string $code;
    public $group;
    public $title;
    public $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;

    public function __construct()
    {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_header_tags_breadcrumb_title');
      $this->description = CLICSHOPPING::getDef('module_header_tags_breadcrump_description');

      if (\defined('MODULE_HEADER_TAGS_BREADCRUMB_STATUS')) {
        $this->sort_order = MODULE_HEADER_TAGS_BREADCRUMB_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_BREADCRUMB_STATUS == 'True');
      }
    }

    public function execute()
    {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
      $CLICSHOPPING_Service = Registry::get('Service');

      if ($CLICSHOPPING_Service->isStarted('Breadcrumb')) {
        $footer = $CLICSHOPPING_Breadcrumb->getJsonBreadcrumb();
      }

      $CLICSHOPPING_Template->addBlock($footer, 'footer_scripts');
    }

    public function isEnabled()
    {
      return $this->enabled;
    }

    public function check()
    {
    {
      return \defined('MODULE_HEADER_TAGS_BREADCRUMB_STATUS');
    }

    public function install()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to display this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_BREADCRUMB_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to display this module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort Order',
          'configuration_key' => 'MODULE_HEADER_TAGS_BREADCRUMB_SORT_ORDER',
          'configuration_value' => '555',
          'configuration_description' => 'Sort order. Lowest is displayed in first',
          'configuration_group_id' => '6',
          'sort_order' => '10',
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
      return ['MODULE_HEADER_TAGS_BREADCRUMB_STATUS',
              'MODULE_HEADER_TAGS_BREADCRUMB_SORT_ORDER'
             ];
    }
  }
