<?php
  /**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class chs_thank_you {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_checkout_success_thank_you_title');
      $this->description = CLICSHOPPING::getDef('module_checkout_success_thank_you_description');

      if ( defined('MODULE_CHECKOUT_SUCCESS_THANK_YOU_STATUS') ) {
        $this->sort_order = defined('MODULE_CHECKOUT_SUCCESS_THANK_YOU_SORT_ORDER') ? MODULE_CHECKOUT_SUCCESS_THANK_YOU_SORT_ORDER : 0;
        $this->enabled = (MODULE_CHECKOUT_SUCCESS_THANK_YOU_STATUS == 'True');
      }
    }

    public function execute() {

      $CLICSHOPPING_Template = Registry::get('Template');

      if (isset($_GET['Checkout']) && isset($_GET['Success'])) {

        $content_width = (int)MODULE_CHECKOUT_SUCCESS_THANK_YOU_CONTENT_WIDTH;

        $thank_you = '<!-- cs_thank_you start -->' . "\n";

        ob_start();

        require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/thank_you'));
        $thank_you .= ob_get_clean();

        $thank_you .= '<!-- cs_thank_you end -->' . "\n";

        $CLICSHOPPING_Template->addBlock($thank_you, $this->group);
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_CHECKOUT_SUCCESS_THANK_YOU_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Enable Product Downloads Module',
          'configuration_key' => 'MODULE_CHECKOUT_SUCCESS_THANK_YOU_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Should ordered product download links be shown on the checkout success page ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Select the width to display?',
          'configuration_key' => 'MODULE_CHECKOUT_SUCCESS_THANK_YOU_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'select a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort Order',
          'configuration_key' => 'MODULE_CHECKOUT_SUCCESS_THANK_YOU_SORT_ORDER',
          'configuration_value' => '1',
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
      return array('MODULE_CHECKOUT_SUCCESS_THANK_YOU_STATUS',
                   'MODULE_CHECKOUT_SUCCESS_THANK_YOU_CONTENT_WIDTH',
                   'MODULE_CHECKOUT_SUCCESS_THANK_YOU_SORT_ORDER'
                  );
    }
  }

