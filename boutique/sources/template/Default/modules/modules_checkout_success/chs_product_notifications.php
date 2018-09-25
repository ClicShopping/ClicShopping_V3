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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Sites\Shop\Pages\Account\Classes\Notifications;
  use ClicShopping\Sites\Shop\Pages\Checkout\Classes\CheckoutSuccess;

  class chs_product_notifications {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_checkout_success_product_notifications_title');
      $this->description = CLICSHOPPING::getDef('module_checkout_success_product_notification_description');

      if ( defined('MODULE_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS') ) {
        $this->sort_order = defined('MODULE_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_SORT_ORDER') ? MODULE_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_SORT_ORDER : 0;
        $this->enabled = (MODULE_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if ( isset($_GET['Checkout']) && isset($_GET['Success'])) {
        $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_success_action'), 'success', 'checkout_success');

        $order_id = CheckoutSuccess::getCheckoutSuccessOrderId();

        $content_width = (int)MODULE_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_CONTENT_WIDTH;

        $form = HTML::form('order', CLICSHOPPING::link('index.php', 'Checkout&Success&action=update'), 'post');
        $endform = '</form>';

/*
        $QglobalNotifications = $CLICSHOPPING_Db->prepare('select global_product_notifications
                                                    from :table_customers_info
                                                    where customers_info_id = :customers_info_id
                                                  ');
        $QglobalNotifications->bindInt(':customers_info_id', $CLICSHOPPING_Customer->getID() );

        $QglobalNotifications->execute();

        if ($QglobalNotifications->valueInt('global_product_notifications') != 1) {
          if (isset($_GET['action']) && ($_GET['action'] == 'update')) {
              if ( isset($_POST['notify']) && is_array($_POST['notify']) && !empty($_POST['notify']) ) {
                $notify = array_unique($_POST['notify']);

                foreach ( $notify as $n ) {
                  if ( is_numeric($n) && ($n > 0) ) {
                   $Qcheck = $CLICSHOPPING_Db->get('products_notifications', 'products_id', ['products_id' => (int)$n,
                                                                                       'customers_id' => (int)$CLICSHOPPING_Customer->getID()],
                                                                                       null,
                                                                                       1
                                           );

                     if ( $Qcheck->fetch() === false ) {
                       $CLICSHOPPING_Db->save('products_notifications', ['products_id' => (int)$n,
                                                                  'customers_id' => (int)$CLICSHOPPING_Customer->getID(),
                                                                  'date_added' => 'now()'
                                                                 ]
                                      );

                     }
                  }
                }
              }
            }
*/

          $products_displayed = [];

          $Qproducts = $CLICSHOPPING_Db->prepare('select products_id,
                                                         products_name
                                                  from :table_orders_products
                                                  where orders_id = :orders_id
                                                  order by products_name
                                                ');
          $Qproducts->bindInt(':orders_id', $order_id);

          $Qproducts->execute();

            while ($Qproducts->fetch()) {
              if (!isset($products_displayed[$Qproducts->valueInt('products_id')])) {
                $products_id = $Qproducts->valueInt('products_id');

                if (Notifications::getGlobalProductNotificationsProduct($products_id)) {
                  $check = true;
                }

                $products_displayed[$Qproducts->valueInt('products_id')] = '<label class="checkbox-inline"> ' . HTML::checkboxField('notify[]', $products_id, $check) . ' ' . $Qproducts->value('products_name') . '</label>';
                $products_displayed[$Qproducts->valueInt('products_id')] .= HTML::hiddenField('products_id[]', $Qproducts->valueInt('products_id'));
              }
            }

            $products_notifications = implode('<br />', $products_displayed);


            $notification = '<!-- Start product notification -->';

            ob_start();

            require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/product_notifications'));
            $notification .= ob_get_clean();


            $notification .= '<!-- Product notification end -->' . "\n";

            $CLICSHOPPING_Template->addBlock($notification, $this->group);
//          }
        }
      }


    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS');
    }

    public function install() {

      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Enable Product Downloads Module',
          'configuration_key' => 'MODULE_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Should ordered product download links be shown on the checkout success page ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
        'configuration_title' => 'Please select the width to display ?',
        'configuration_key' => 'MODULE_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_CONTENT_WIDTH',
        'configuration_value' => '12',
        'configuration_description' => 'Select a number between 1 and 12',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'clic_cfg_set_content_module_width_pull_down',
        'date_added' => 'now()'
      ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort Order',
          'configuration_key' => 'MODULE_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_SORT_ORDER',
          'configuration_value' => '50',
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
      return array('MODULE_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS',
                   'MODULE_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_CONTENT_WIDTH',
                   'MODULE_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_SORT_ORDER'
                  );
    }
  }
