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
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\Sites\Shop\AddressBook;

  class ac_account_customers_list_order {

    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_account_customers_list_order_title');
      $this->description = CLICSHOPPING::getDef('module_account_customers_list_order_description');


      if (defined('MODULE_ACCOUNT_CUSTOMERS_LIST_ORDER_TITLE_STATUS')) {
        $this->sort_order = MODULE_ACCOUNT_CUSTOMERS_LIST_ORDER_TITLE_SORT_ORDER;
        $this->enabled = (MODULE_ACCOUNT_CUSTOMERS_LIST_ORDER_TITLE_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db  = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if ((isset($_GET['Account']) &&  isset($_GET['Main'])) || (isset($_GET['Account']) &&  isset($_GET['Login']))) {

        $content_width = (int)MODULE_ACCOUNT_CUSTOMERS_LIST_ORDER_CONTENT_WIDTH;

        $Qorders = $CLICSHOPPING_Db->prepare('select o.orders_id,
                                                     o.date_purchased,
                                                     o.delivery_name,
                                                     o.delivery_country,
                                                     o.billing_name,
                                                     o.billing_country,
                                                     ot.text as order_total,
                                                     s.orders_status_name
                                             from :table_orders o,
                                                  :table_orders_total ot,
                                                  :table_orders_status s
                                             where o.customers_id = :customers_id
                                             and o.orders_id = ot.orders_id
                                             and (ot.class = :class or ot.class = :class1)
                                             and o.orders_status = s.orders_status_id
                                             and s.language_id = :language_id
                                             and s.public_flag = :public_flag
                                             order by orders_id desc limit 5
                                            ');

          $Qorders->bindInt(':customers_id', (int)$CLICSHOPPING_Customer->getID());
          $Qorders->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
          $Qorders->bindValue(':public_flag', '1');
          $Qorders->bindValue(':class', 'ot_total');
          $Qorders->bindValue(':class1', 'TO');

          $Qorders->execute();

          $account_customers_title_content = '<!-- Start account_customers_title -->' . "\n";
          $account_customers_title_content .= '<div class="col-md-12">';

          $account_customers_title_content .= '<div class="card">
                                                <div class="card-header">
                                                  <div class="row">
                                                    <div class="col-md-11 ModuleAccountCustomersListOrderTitle"><h3>'. CLICSHOPPING::getDef('module_account_customers_list_order_order') .'</h3></div>
                                                      <div class="col-md-1 text-md-right">
                                                        <i class="fas fa-history fa-4x moduleAccountCustomersNotificationsIcon"></i>
                                                      </div>
                                                    </div>
                                                  </div>
                                                  <div class="card-block">
                                                  <div class="card-text">
                                              ';

          if (AddressBook::countCustomerOrders() > 0) {
            $account_customers_title_content .= '<div class="ModuleAccountCustomersListOrderCustomer">';
            $account_customers_title_content .= '<div class="col-md-2">';
            $account_customers_title_content .= '<strong>' . CLICSHOPPING::getDef('overview_title') .'</strong>';
            $account_customers_title_content .= '</div>';
            $account_customers_title_content .= '<div class="separator"></div>';
            $account_customers_title_content .= '<div class="col-md-8">';
            $account_customers_title_content .= HTML::link(CLICSHOPPING::link(null, 'Account&History'), '<u>' . CLICSHOPPING::getDef('overview_show_all_orders') . '</u>');
            $account_customers_title_content .= '</div>';
            $account_customers_title_content .=  '<div class="separator"></div>';
            $account_customers_title_content .= '<div class="col-md-10">';
            $account_customers_title_content .= '<p><strong>' . CLICSHOPPING::getDef('overview_previous_orders') . '</strong></p>';
            $account_customers_title_content .= '</div>';
            $account_customers_title_content .= '<div class="separator"></div>';

            $account_customers_title_content .= '<div class="d-flex flex-wrap">';

            while ($Qorders->fetch()) {
               if (!empty($Qorders->value('delivery_name'))) {
                 $order_name = $Qorders->value('delivery_name');
                 $order_country = $Qorders->value('delivery_country');
               } else {
                 $order_name = $Qorders->value('billing_name');
                 $order_country = $Qorders->value('billing_country');
               }

               $account_customers_title_content .= '<div class="col-md-12">';
               $account_customers_title_content .= '<span class="col-md-3">' . DateTime::toShort($Qorders->value('date_purchased')) . '</span>';
               $account_customers_title_content .= '<span class="col-md-1">#' . $Qorders->valueInt('orders_id') . '</span>';
               $account_customers_title_content .= '<span class="col-md-3">' . HTML::outputProtected($order_name) . ', ' .  HTML::outputProtected($order_country) . '</span>';
               $account_customers_title_content .= '<span class="col-md-2">' . $Qorders->value('orders_status_name') . '</span>';
               $account_customers_title_content .= '<span class="col-md-1 float-md-right">';
               $account_customers_title_content .= '<p class="float-md-right">'. HTML::button(CLICSHOPPING::getDef('button_view'), null, CLICSHOPPING::link(null, 'Account&HistoryInfo&order_id=' . (int)$Qorders->valueInt('orders_id')),'info', null, 'sm').'</p>';
               $account_customers_title_content .= '</span>';
               $account_customers_title_content .= '<span class="col-md-2 float-md-right">' . $Qorders->value('order_total'). '</span>';
               $account_customers_title_content .= '</div>';
            } // end while

            $account_customers_title_content .= '<div class="col-md-12">';
            $account_customers_title_content .= '<div>'. CLICSHOPPING::getDef('module_account_customers_list_order_order_text') .'</div>';
            $account_customers_title_content .= '<div class="separator"></div>';
            $account_customers_title_content .= '</div>';
            $account_customers_title_content .= '</div>';
            $account_customers_title_content .= '</div>';
         }

         $account_customers_title_content .= '<div class="hr"></div>';
         $account_customers_title_content .= '</div>' . "\n";
         $account_customers_title_content .= '</div>' . "\n";
         $account_customers_title_content .= '</div>' . "\n";
         $account_customers_title_content .= '</div>' . "\n";

         $account_customers_title_content .= '<!-- end account_customers_title -->' . "\n";

         $CLICSHOPPING_Template->addBlock($account_customers_title_content, $this->group);
      } // php_self
    } // function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_ACCOUNT_CUSTOMERS_LIST_ORDER_TITLE_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULE_ACCOUNT_CUSTOMERS_LIST_ORDER_TITLE_STATUS',
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
          'configuration_key' => 'MODULE_ACCOUNT_CUSTOMERS_LIST_ORDER_CONTENT_WIDTH',
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
          'configuration_key' => 'MODULE_ACCOUNT_CUSTOMERS_LIST_ORDER_TITLE_SORT_ORDER',
          'configuration_value' => '10',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '10',
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
        'MODULE_ACCOUNT_CUSTOMERS_LIST_ORDER_TITLE_STATUS',
        'MODULE_ACCOUNT_CUSTOMERS_LIST_ORDER_CONTENT_WIDTH',
        'MODULE_ACCOUNT_CUSTOMERS_LIST_ORDER_TITLE_SORT_ORDER'
      );
    }
  }
