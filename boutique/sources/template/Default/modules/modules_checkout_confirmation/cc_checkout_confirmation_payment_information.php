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

  use ClicShopping\Apps\Marketing\BannerManager\Classes\Shop\Banner;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class cc_checkout_confirmation_payment_information {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_checkout_confirmation_payment_information_title');
      $this->description = CLICSHOPPING::getDef('module_checkout_confirmation_payment_information_description');

      if (defined('MODULE_CHECKOUT_CONFIRMATION_PAYMENT_INFORMATION_STATUS')) {
        $this->sort_order = MODULE_CHECKOUT_CONFIRMATION_PAYMENT_INFORMATION_SORT_ORDER;
        $this->enabled = (MODULE_CHECKOUT_CONFIRMATION_PAYMENT_INFORMATION_STATUS == 'True');
      }
     }

    public function execute() {
      global $confirmation, $CLICSHOPPING_Payment;

      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if (isset($_GET['Checkout']) && isset($_GET['Confirmation']) && $CLICSHOPPING_Customer->isLoggedOn() ) {

        $content_width = (int)MODULE_CHECKOUT_CONFIRMATION_PAYMENT_INFORMATION_CONTENT_WIDTH;

        if (is_array($CLICSHOPPING_Payment->modules)) {

          if ($confirmation) {

            $display = '  <!-- checkout_confirmation_payment_information -->' . "\n";

            if (isset($confirmation['content'])) {
              $display .= '<div class="col-sm-12">';
              $display .= '<div class="checkoutPaymentInput">' . $confirmation['content'] . '</div>';
              $display .= ' </div>';
            } else {
              if (isset($confirmation['title'])) {
                $data  = '<div class="col-sm-12">';
                $data .=  '  <div class="alert alert-danger">';
                $data .=  $confirmation['title'];
                $data .=  '  </div>';
                $data .=  '</div>';
              }

              if (isset($confirmation['fields'])) {

                $fields = '';

                $fields .= '<div class="col-sm-6">';
                $fields .=  '<div class="alert alert-info">';

                for ($i=0, $n=count($confirmation['fields']); $i<$n; $i++) {

                  $fields .= $confirmation['fields'][$i]['title'] . ' ';
                  $data .= $confirmation['fields'][$i]['field'];

                  ob_start();
                  require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/checkout_confirmation_payment_information'));

                  $display .= ob_get_clean();
                  $fields .= '</div>';
                  $fields .= '</div>';
                }
              } else {
                ob_start();
                require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/checkout_confirmation_payment_information'));

                $display .= ob_get_clean();
              }
            }

            $display .= '<!--  checkout_confirmation_payment_information -->' . "\n";

            $CLICSHOPPING_Template->addBlock($display, $this->group);
          }
        }
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_CHECKOUT_CONFIRMATION_PAYMENT_INFORMATION_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULE_CHECKOUT_CONFIRMATION_PAYMENT_INFORMATION_STATUS',
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
          'configuration_key' => 'MODULE_CHECKOUT_CONFIRMATION_PAYMENT_INFORMATION_CONTENT_WIDTH',
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
          'configuration_key' => 'MODULE_CHECKOUT_CONFIRMATION_PAYMENT_INFORMATION_SORT_ORDER',
          'configuration_value' => '85',
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
        'MODULE_CHECKOUT_CONFIRMATION_PAYMENT_INFORMATION_STATUS',
        'MODULE_CHECKOUT_CONFIRMATION_PAYMENT_INFORMATION_CONTENT_WIDTH',
        'MODULE_CHECKOUT_CONFIRMATION_PAYMENT_INFORMATION_SORT_ORDER'
      );
    }
  }