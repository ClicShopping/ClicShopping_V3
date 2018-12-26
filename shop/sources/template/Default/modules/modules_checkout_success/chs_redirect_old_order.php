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
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\CLICSHOPPING;

  class chs_redirect_old_order {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_checkout_success_redirect_old_order_title');
      $this->description = CLICSHOPPING::getDef('module_checkout_success_redirect_old_order_description');

      if ( defined('MODULE_CHECKOUT_SUCCESS_REDIRECT_OLD_ORDER_STATUS') ) {
        $this->sort_order = defined('MODULE_CHECKOUT_SUCCESS_REDIRECT_OLD_ORDER_SORT_ORDER') ? MODULE_CHECKOUT_SUCCESS_REDIRECT_OLD_ORDER_SORT_ORDER : 0;
        $this->enabled = (MODULE_CHECKOUT_SUCCESS_REDIRECT_OLD_ORDER_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Db = Registry::get('Db');

      if (isset($_GET['Checkout']) && isset($_GET['Success'])) {
        if ((int)MODULE_CHECKOUT_SUCCESS_REDIRECT_OLD_ORDER_MINUTES > 0 ) {
          $QLastorder = $CLICSHOPPING_Db->prepare('select orders_id
                                                from :table_orders
                                                order by orders_id DESC
                                                limit 1
                                               ');
          $QLastorder->execute();

          $order_id = $QLastorder->valueInt('orders_id');

          if (!is_null($order_id)) {
            $Qcheck = $CLICSHOPPING_Db->prepare('select 1 from orders
                                                 where orders_id = :orders_id
                                                 and date_purchased < date_sub(now() ),
                                                 interval :interval minute
                                              ');
            $Qcheck->bindInt(':orders_id',(int)$order_id);
            $Qcheck->bindInt(':interval',(int)MODULE_CHECKOUT_SUCCESS_REDIRECT_OLD_ORDER_MINUTES  );

            if ( $Qcheck->fetch() !== false ) {
              CLICSHOPPING::redirect(null, 'Account&Main');
            }
          }
        }
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_CHECKOUT_SUCCESS_REDIRECT_OLD_ORDER_STATUS');
    }

    public function install()  {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Enable Product Downloads Module',
          'configuration_key' => 'MODULE_CHECKOUT_SUCCESS_REDIRECT_OLD_ORDER_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Should ordered product download links be shown on the checkout success page ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please, choose you minutes tor edirect the page',
          'configuration_key' => 'MODULE_CHECKOUT_SUCCESS_REDIRECT_OLD_ORDER_MINUTES',
          'configuration_value' => '60',
          'configuration_description' => 'Redirect customers to the index page after an order older than this amount is viewed',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort Order',
          'configuration_key' => 'MODULE_CHECKOUT_SUCCESS_REDIRECT_OLD_ORDER_SORT_ORDER',
          'configuration_value' => '4',
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
      return array('MODULE_CHECKOUT_SUCCESS_REDIRECT_OLD_ORDER_STATUS',
                   'MODULE_CHECKOUT_SUCCESS_REDIRECT_OLD_ORDER_MINUTES',
                   'MODULE_CHECKOUT_SUCCESS_REDIRECT_OLD_ORDER_SORT_ORDER'
                  );
    }
  }

