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
  use ClicShopping\OM\HTML;

  use ClicShopping\Sites\Shop\AddressBook;

  class cc_checkout_confirmation_billing_address {
    public $code;
    public $group;
    public string $title;
    public string $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_checkout_confirmation_billing_address_title');
      $this->description = CLICSHOPPING::getDef('module_checkout_confirmation_billing_address_description');

      if (\defined('MODULE_CHECKOUT_CONFIRMATION_BILLING_ADDRESS_STATUS')) {
        $this->sort_order = MODULE_CHECKOUT_CONFIRMATION_BILLING_ADDRESS_SORT_ORDER;
        $this->enabled = (MODULE_CHECKOUT_CONFIRMATION_BILLING_ADDRESS_STATUS == 'True');
      }
     }

    public function execute() {

      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Order = Registry::get('Order');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Address = Registry::get('Address');

      if (isset($_GET['Checkout']) && isset($_GET['Confirmation']) && $CLICSHOPPING_Customer->isLoggedOn()) {

        $content_width = (int)MODULE_CHECKOUT_CONFIRMATION_BILLING_ADDRESS_CONTENT_WIDTH;

        $modify_address = AddressBook::countCustomersModifyAddressDefault();
        $edit_payment_address = HTML::link(CLICSHOPPING::link(null, 'Checkout&PaymentAddress'), '<span class="orderEdit">(' . CLICSHOPPING::getDef('module_checkout_confirmation_billing_address_text_edit') . ')</span>');
        $billing_address = $CLICSHOPPING_Address->addressFormat($CLICSHOPPING_Order->billing['format_id'], $CLICSHOPPING_Order->billing, 1, ' ', '<br />');
        $payment_method  = HTML::link(CLICSHOPPING::link(null, 'Checkout&Billing'), '<span class="orderEdit">(' . CLICSHOPPING::getDef('module_checkout_confirmation_billing_address_text_edit') . ')</span>');
        $type_payment = $_SESSION['payment'];

        $confirmation = '  <!-- cc_checkout_confirmation_billing_address start -->' . "\n";

        ob_start();
        require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/checkout_confirmation_billing_address'));

        $confirmation .= ob_get_clean();

        $confirmation .= '<!--  cc_checkout_confirmation_billing_address end -->' . "\n";

        $CLICSHOPPING_Template->addBlock($confirmation, $this->group);
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return \defined('MODULE_CHECKOUT_CONFIRMATION_BILLING_ADDRESS_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_CHECKOUT_CONFIRMATION_BILLING_ADDRESS_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the module',
          'configuration_key' => 'MODULE_CHECKOUT_CONFIRMATION_BILLING_ADDRESS_CONTENT_WIDTH',
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
          'configuration_key' => 'MODULE_CHECKOUT_CONFIRMATION_BILLING_ADDRESS_SORT_ORDER',
          'configuration_value' => '20',
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
      return array(
        'MODULE_CHECKOUT_CONFIRMATION_BILLING_ADDRESS_STATUS',
        'MODULE_CHECKOUT_CONFIRMATION_BILLING_ADDRESS_CONTENT_WIDTH',
        'MODULE_CHECKOUT_CONFIRMATION_BILLING_ADDRESS_SORT_ORDER'
      );
    }
  }