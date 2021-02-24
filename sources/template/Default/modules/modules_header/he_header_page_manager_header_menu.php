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

  class he_header_page_manager_header_menu {
    public string $code;
    public string $group;
    public string $title;
    public string $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;
    public $pages;

    public function __construct() {

      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_header_page_manager_header_menu_title');
      $this->description = CLICSHOPPING::getDef('module_header_page_manager_header_menu_description');

      if (\defined('MODULE_HEADER_PAGE_MANAGER_HEADER_MENU_STATUS')) {
        $this->sort_order = MODULE_HEADER_PAGE_MANAGER_HEADER_MENU_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_PAGE_MANAGER_HEADER_MENU_STATUS == 'True');
        $this->pages = MODULE_HEADER_PAGE_MANAGER_HEADER_MENU_DISPLAY_PAGES;
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_PageManagerShop = Registry::get('PageManagerShop');

      if (MODULE_HEADER_PAGE_MANAGER_HEADER_MENU_STATUS == 'True') {
        $content_width = (int)MODULE_HEADER_PAGE_MANAGER_HEADER_MENU_CONTENT_WIDTH;
        $header_menu =  $CLICSHOPPING_PageManagerShop->pageManagerDisplayHeaderMenu('<div class="menuHeaderPageManager">', '</div>');

        $data ='<!-- Boxe page manager menu header  start -->' . "\n";

        ob_start();

        require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/header_page_manager_header_menu'));

        $data .= ob_get_clean();

        $data .='<!-- Boxe  page manager menu header  end -->' . "\n";

        $CLICSHOPPING_Template->addBlock($data, $this->group);
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return \defined('MODULE_HEADER_PAGE_MANAGER_HEADER_MENU_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_HEADER_PAGE_MANAGER_HEADER_MENU_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please, select the width of your module ?',
          'configuration_key' => 'MODULE_HEADER_PAGE_MANAGER_HEADER_MENU_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Indicate a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order display',
          'configuration_key' => 'MODULE_HEADER_PAGE_MANAGER_HEADER_MENU_SORT_ORDER',
          'configuration_value' => '50',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Indicate the page where the module is displayed',
          'configuration_key' => 'MODULE_HEADER_PAGE_MANAGER_HEADER_MENU_DISPLAY_PAGES',
          'configuration_value' => 'all',
          'configuration_description' => 'Select the page where the module is displayed.',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => 'clic_cfg_set_select_pages_list',
          'date_added' => 'now()'
        ]
      );

    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array('MODULE_HEADER_PAGE_MANAGER_HEADER_MENU_STATUS',
                   'MODULE_HEADER_PAGE_MANAGER_HEADER_MENU_CONTENT_WIDTH',
                   'MODULE_HEADER_PAGE_MANAGER_HEADER_MENU_SORT_ORDER',
                   'MODULE_HEADER_PAGE_MANAGER_HEADER_MENU_DISPLAY_PAGES'
                  );
    }
  }
