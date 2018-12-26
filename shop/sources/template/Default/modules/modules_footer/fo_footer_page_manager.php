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

  use ClicShopping\Apps\Communication\PageManager\Classes\Shop\PageManagerShop;

  class fo_footer_page_manager {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;
    public $pages;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);
      $this->title = CLICSHOPPING::getDef('module_footer_page_manager_title');
      $this->description = CLICSHOPPING::getDef('module_footer_page_manager_description');

      if ( defined('MODULES_FOOTER_PAGE_MANAGER_STATUS') ) {
        $this->sort_order = MODULES_FOOTER_PAGE_MANAGER_SORT_ORDER;
        $this->enabled = (MODULES_FOOTER_PAGE_MANAGER_STATUS == 'True');
        $this->pages = MODULE_FOOTER_PAGE_MANAGER_DISPLAY_PAGES;
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_PageManagerShop = Registry::get('PageManagerShop');
      $CLICSHOPPING_Db = Registry::get('Db');

      if  ( MODE_VENTE_PRIVEE == 'false' || (MODE_VENTE_PRIVEE == 'true' && $CLICSHOPPING_Customer->isLoggedOn() )) {

        $Qpages = $CLICSHOPPING_Db->prepare('select count(*) as count
                                            from :table_pages_manager
                                            where status = 1
                                            and page_box = 0
                                            and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                           ');
        $Qpages->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID() );

        $Qpages->execute();


        if ( $Qpages->valueInt('count') > 0) {

          $content_width = (int)MODULE_FOOTER_PAGE_MANAGER_CONTENT_WIDTH;

          $link = $CLICSHOPPING_PageManagerShop->pageManagerDisplayFooter();

          $page_manager_footer = '<!-- footer page manager start -->' . "\n";

          ob_start();

          require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/footer_page_manager'));

          $page_manager_footer .= ob_get_clean();

          $page_manager_footer .='<!-- footer page manager end -->' . "\n";

          $CLICSHOPPING_Template->addBlock($page_manager_footer, $this->group);
        }
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULES_FOOTER_PAGE_MANAGER_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULES_FOOTER_PAGE_MANAGER_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want activate this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the module',
          'configuration_key' => 'MODULE_FOOTER_PAGE_MANAGER_CONTENT_WIDTH',
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
          'configuration_key' => 'MODULES_FOOTER_PAGE_MANAGER_SORT_ORDER',
          'configuration_value' => '10',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Indicate the page where the module is displayed',
          'configuration_key' => 'MODULE_FOOTER_PAGE_MANAGER_DISPLAY_PAGES',
          'configuration_value' => 'all',
          'configuration_description' => 'Select the page where the module is displayed.',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => 'clic_cfg_set_select_pages_list',
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
      return array('MODULES_FOOTER_PAGE_MANAGER_STATUS',
                   'MODULE_FOOTER_PAGE_MANAGER_CONTENT_WIDTH',
                   'MODULES_FOOTER_PAGE_MANAGER_SORT_ORDER',
                   'MODULE_FOOTER_PAGE_MANAGER_DISPLAY_PAGES'
                  );
    }
  }
?>
