<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTTP;

  class sf_footer_suffix_copyright {
    public string $code;
    public string $group;
    public $title;
    public $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('modules_footer_suffix_copyright_title');
      $this->description = CLICSHOPPING::getDef('modules_footer_suffix_copyright_description');

      if (\defined('MODULES_FOOTER_SUFFIX_COPYRIGHT_STATUS')) {
        $this->sort_order = MODULES_FOOTER_SUFFIX_COPYRIGHT_SORT_ORDER;
        $this->enabled = (MODULES_FOOTER_SUFFIX_COPYRIGHT_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');

      $logo = '<img width="24" height="24" alt="ClicShopping, Free E-commerce Open Source Solution B2B - B2C for everybody" title="ClicShopping, Free E-commerce Open Source Solution B2B - B2C for everybody" src="' . HTTP::getShopUrlDomain() .'images/logo_clicshopping_24.webp">';
      $clicshopping_copyright = date('Y');
      $shop_owner_copyright = date('Y') . ' - ' . STORE_NAME;

      $footer_copyright = '<!-- footer copyright start -->' . "\n";

      ob_start();
      require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/suffix_footer_copyright'));

      $footer_copyright .= ob_get_clean();

      $footer_copyright .= '<!-- footer copyright end -->' . "\n";

      $CLICSHOPPING_Template->addBlock($footer_copyright, $this->group);
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return \defined('MODULES_FOOTER_SUFFIX_COPYRIGHT_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULES_FOOTER_SUFFIX_COPYRIGHT_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULES_FOOTER_SUFFIX_COPYRIGHT_SORT_ORDER',
          'configuration_value' => '100',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array('MODULES_FOOTER_SUFFIX_COPYRIGHT_STATUS',
                   'MODULES_FOOTER_SUFFIX_COPYRIGHT_SORT_ORDER'
                  );
    }
  }
