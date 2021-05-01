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


  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class ht_googlefont
  {
    public $code;
    public $group;
    public string $title;
    public string $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;

    public function __construct()
    {
      $this->code = get_class($this);
      $this->group = 'header_tags';
      $this->title = CLICSHOPPING::getDef('module_header_tags_google_font_title');
      $this->description = CLICSHOPPING::getDef('module_header_tags_google_font_description');

      if (\defined('MODULE_HEADER_TAGS_GOOGLE_FONT_STATUS')) {
        $this->sort_order = MODULE_HEADER_TAGS_GOOGLE_FONT_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_GOOGLE_FONT_STATUS == 'True');
      }
    }

    public function execute()
    {
      $CLICSHOPPING_Template = Registry::get('Template');
      $google = '<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>';

      $CLICSHOPPING_Template->addBlock($google . "\n", $this->group);
    }

    public function isEnabled()
    {
      return $this->enabled;
    }

    public function check()
    {
      return \defined('MODULE_HEADER_TAGS_GOOGLE_FONT_STATUS');
    }

    public function install()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to install this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_GOOGLE_FONT_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to install this module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort Order',
          'configuration_key' => 'MODULE_HEADER_TAGS_GOOGLE_FONT_SORT_ORDER',
          'configuration_value' => '50',
          'configuration_description' => 'Sort order. Lowest is displayed in first',
          'configuration_group_id' => '6',
          'sort_order' => '25',
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
      return array('MODULE_HEADER_TAGS_GOOGLE_FONT_STATUS',
        'MODULE_HEADER_TAGS_GOOGLE_FONT_SORT_ORDER');
    }
  }

