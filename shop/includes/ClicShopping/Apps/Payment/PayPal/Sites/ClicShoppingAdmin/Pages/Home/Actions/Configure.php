<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Payment\PayPal\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class Configure extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_PayPal = Registry::get('PayPal');
      $CLICSHOPPING_Language = Registry::get('Language');

      $this->page->setFile('configure.php');
      $this->page->data['action'] = 'Configure';

      $CLICSHOPPING_PayPal->loadDefinitions('ClicShoppingAdmin/configure');

      $modules = $CLICSHOPPING_PayPal->getConfigModules();

      $default_module = 'G';

      foreach ($modules as $m) {
        if ($CLICSHOPPING_PayPal->getConfigModuleInfo($m, 'is_installed') === true) {
          $default_module = $m;
          break;
        }
      }

      $this->page->data['current_module'] = (isset($_GET['module']) && in_array($_GET['module'], $modules)) ? $_GET['module'] : $default_module;

      if (!defined('CLICSHOPPING_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID')) {
        $Qcheck = $CLICSHOPPING_PayPal->db->get('orders_status', 'orders_status_id', [
          'orders_status_name' => 'PayPal [Transactions]'
        ],
          null,
          1
        );

        if ($Qcheck->fetch() === false) {
          $Qstatus = $CLICSHOPPING_PayPal->db->get('orders_status', 'max(orders_status_id) as status_id');

          $status_id = $Qstatus->valueInt('status_id') + 1;

          $languages = $CLICSHOPPING_Language->getLanguages();

          foreach ($languages as $lang) {

            $CLICSHOPPING_PayPal->db->save('orders_status', [
                'orders_status_id' => $status_id,
                'language_id' => $lang['id'],
                'orders_status_name' => 'PayPal [Transactions]',
                'public_flag' => 0,
                'downloads_flag' => 0
              ]
            );

          }

        } else {
          $status_id = $Qcheck->valueInt('orders_status_id');
        }

        $CLICSHOPPING_PayPal->saveCfgParam('CLICSHOPPING_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID', $status_id);
      }

      if (!defined('CLICSHOPPING_APP_PAYPAL_GATEWAY')) {
        $CLICSHOPPING_PayPal->saveCfgParam('CLICSHOPPING_APP_PAYPAL_GATEWAY', '1');
      }

      if (!defined('CLICSHOPPING_APP_PAYPAL_LOG_TRANSACTIONS')) {
        $CLICSHOPPING_PayPal->saveCfgParam('CLICSHOPPING_APP_PAYPAL_LOG_TRANSACTIONS', '1');
      }
    }
  }
