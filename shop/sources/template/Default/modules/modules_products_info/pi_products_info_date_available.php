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

  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class pi_products_info_date_available {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_products_info_date_available');
      $this->description = CLICSHOPPING::getDef('module_products_info_date_available_description');

      if (defined('MODULE_PRODUCTS_INFO_DATE_AVAILABLE_STATUS')) {
        $this->sort_order = MODULE_PRODUCTS_INFO_DATE_AVAILABLE_SORT_ORDER;
        $this->enabled = (MODULE_PRODUCTS_INFO_DATE_AVAILABLE_STATUS == 'True');
      }
    }

    public function execute() {

      if (isset($_GET['products_id']) && isset($_GET['Products']) ) {
        $content_width = (int)MODULE_PRODUCTS_INFO_DATE_AVAILABLE_CONTENT_WIDTH;
        $text_position = MODULE_PRODUCTS_INFO_DATE_AVAILABLE_POSITION;

        $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
        $CLICSHOPPING_Template = Registry::get('Template');

        $products_date_available = $CLICSHOPPING_ProductsCommon->getProductsDateAvailable();

        if ($products_date_available > date('Y-m-d H:i:s')) {
          $products_date_available = CLICSHOPPING::getDef('text_date_available', ['date' => DateTime::toshort($products_date_available)]);
        }

        $products_date_available_content = '<!-- Start products_date_available -->' . "\n";

        ob_start();
        require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/products_info_date_available'));
        $products_date_available_content .= ob_get_clean();

        $products_date_available_content .= '<!-- products_date_available -->' . "\n";

        $CLICSHOPPING_Template->addBlock($products_date_available_content, $this->group);
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_PRODUCTS_INFO_DATE_AVAILABLE_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_DATE_AVAILABLE_STATUS',
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
          'configuration_key' => 'MODULE_PRODUCTS_INFO_DATE_AVAILABLE_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Veuillez indiquer un nombre compris entre 1 et 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Where do youw want display this module ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_DATE_AVAILABLE_POSITION',
          'configuration_value' => 'float-md-none',
          'configuration_description' => 'Display the module in function your choice.',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'float-md-right\', \'float-md-left\', \'float-md-none\'),',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_DATE_AVAILABLE_SORT_ORDER',
          'configuration_value' => '100',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '3',
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
        'MODULE_PRODUCTS_INFO_DATE_AVAILABLE_STATUS',
        'MODULE_PRODUCTS_INFO_DATE_AVAILABLE_CONTENT_WIDTH',
        'MODULE_PRODUCTS_INFO_DATE_AVAILABLE_POSITION',
        'MODULE_PRODUCTS_INFO_DATE_AVAILABLE_SORT_ORDER'
      );
    }
  }
