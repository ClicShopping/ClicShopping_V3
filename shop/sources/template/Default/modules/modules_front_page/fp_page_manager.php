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

  class fp_page_manager {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_front_page_manager_title');
      $this->description = CLICSHOPPING::getDef('module_front_page_manager_description');

      if (defined('MODULE_FRONT_PAGE_PAGE_MANAGER_STATUS')) {
        $this->sort_order = MODULE_FRONT_PAGE_PAGE_MANAGER_SORT_ORDER;
        $this->enabled = (MODULE_FRONT_PAGE_PAGE_MANAGER_STATUS == 'True');
      }
    }

    public function execute() {
       $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Category = Registry::get('Category');
      $CLICSHOPPING_PageManagerShop = Registry::get('PageManagerShop');

      if (CLICSHOPPING::getBaseNameIndex() && !$CLICSHOPPING_Category->getPath()) {

          $content_width = (int)MODULE_FRONT_PAGE_PAGE_MANAGER_CONTENT_WIDTH;

// Recuperation de la page d'acceuil personnalisee
         if (!empty($CLICSHOPPING_PageManagerShop->pageManagerDisplayFrontPage() )) {
           $page_manager_front_page = $CLICSHOPPING_PageManagerShop->pageManagerDisplayFrontPage();

           $page_manager_content = '<!-- page_manager_content start -->' . "\n";

           ob_start();
           require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/page_manager'));
           $page_manager_content = ob_get_clean();

           $page_manager_content .= '<!-- page_manager_content end -->' . "\n";

           $CLICSHOPPING_Template->addBlock($page_manager_content, $this->group);
         }
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_FRONT_PAGE_PAGE_MANAGER_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULE_FRONT_PAGE_PAGE_MANAGER_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want activate this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the module width',
          'configuration_key' => 'MODULE_FRONT_PAGE_PAGE_MANAGER_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Select a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_FRONT_PAGE_PAGE_MANAGER_SORT_ORDER',
          'configuration_value' => '10',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                               ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
      );

    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_FRONT_PAGE_PAGE_MANAGER_STATUS',
        'MODULE_FRONT_PAGE_PAGE_MANAGER_CONTENT_WIDTH',
        'MODULE_FRONT_PAGE_PAGE_MANAGER_SORT_ORDER'
      );
    }
  }
