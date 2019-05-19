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

  namespace ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Config\PS;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;


  class PS extends \ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Config\ConfigAbstract
  {
    protected $pm_code = 'paypal_standard';

    public $is_uninstallable = true;
    public $is_migratable = true;
    public $sort_order = 400;

    protected function init()
    {

//      $CLICSHOPPING_Customer = Registry::get('Customer');

      $this->title = $this->app->getDef('module_ps_title');
      $this->short_title = $this->app->getDef('module_ps_short_title');
      $this->introduction = $this->app->getDef('module_ps_introduction');

      $this->is_installed = defined('CLICSHOPPING_APP_PAYPAL_PS_STATUS') && (trim(CLICSHOPPING_APP_PAYPAL_PS_STATUS) != '');

      if (!function_exists('curl_init')) {
        $this->req_notes[] = $this->app->getDef('module_ps_error_curl');
      }

      if (!$this->app->hasCredentials('PS', 'email')) {
        $this->req_notes[] = $this->app->getDef('module_ps_error_credentials');
      }
    }

    public function install()
    {
      parent::install();

      if (defined('MODULE_PAYMENT_INSTALLED')) {
        $installed = explode(';', MODULE_PAYMENT_INSTALLED);
      }

      $installed[] = $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code;

      $this->app->saveCfgParam('MODULE_PAYMENT_INSTALLED', implode(';', $installed));
    }

    public function uninstall()
    {
      parent::uninstall();

      $installed = explode(';', MODULE_PAYMENT_INSTALLED);
      $installed_pos = array_search($this->app->vendor . '\\' . $this->app->code . '\\' . $this->code, $installed);

      if ($installed_pos !== false) {
        unset($installed[$installed_pos]);

        $this->app->saveCfgParam('MODULE_PAYMENT_INSTALLED', implode(';', $installed));
      }
    }

    public function canMigrate()
    {
      $class = $this->pm_code;

      if (is_file(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/modules/payment/' . $class . '.php')) {
        if (!class_exists($class)) {
          include_once(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/modules/payment/' . $class . '.php');
        }

        $module = new $class();

        if (isset($module->signature)) {
          $sig = explode('|', $module->signature);

          if (isset($sig[0]) && ($sig[0] == 'paypal') && isset($sig[1]) && ($sig[1] == $class) && isset($sig[2])) {
            return version_compare($sig[2], 4) >= 0;
          }
        }
      }

      return false;
    }

    public function migrate()
    {
      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_GATEWAY_SERVER')) {
        $server = (MODULE_PAYMENT_PAYPAL_STANDARD_GATEWAY_SERVER == 'Live') ? 'LIVE' : 'SANDBOX';

        if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_ID')) {
          if (!is_null(MODULE_PAYMENT_PAYPAL_STANDARD_ID)) {
            if (!defined('CLICSHOPPING_APP_PAYPAL_' . $server . '_SELLER_EMAIL') || !!is_null(constant('CLICSHOPPING_APP_PAYPAL_' . $server . '_SELLER_EMAIL'))) {
              $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_' . $server . '_SELLER_EMAIL', MODULE_PAYMENT_PAYPAL_STANDARD_ID);
            }
          }

          $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_ID');
        }

        if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_PRIMARY_ID')) {
          if (!is_null(MODULE_PAYMENT_PAYPAL_STANDARD_PRIMARY_ID)) {
            if (!defined('CLICSHOPPING_APP_PAYPAL_' . $server . '_SELLER_EMAIL_PRIMARY') || !!is_null(constant('CLICSHOPPING_APP_PAYPAL_' . $server . '_SELLER_EMAIL_PRIMARY'))) {
              $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_' . $server . '_SELLER_EMAIL_PRIMARY', MODULE_PAYMENT_PAYPAL_STANDARD_PRIMARY_ID);
            }
          }

          $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_PRIMARY_ID');
        }
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_PAGE_STYLE')) {
        $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_PS_PAGE_STYLE', MODULE_PAYMENT_PAYPAL_STANDARD_PAGE_STYLE);
        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_PAGE_STYLE');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_TRANSACTION_METHOD')) {
        $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_PS_TRANSACTION_METHOD', (MODULE_PAYMENT_PAYPAL_STANDARD_TRANSACTION_METHOD == 'Sale') ? '1' : '0');
        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_TRANSACTION_METHOD');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_PREPARE_ORDER_STATUS_ID')) {
        $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_PS_PREPARE_ORDER_STATUS_ID', MODULE_PAYMENT_PAYPAL_STANDARD_PREPARE_ORDER_STATUS_ID);
        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_PREPARE_ORDER_STATUS_ID');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_ORDER_STATUS_ID')) {
        $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_PS_ORDER_STATUS_ID', MODULE_PAYMENT_PAYPAL_STANDARD_ORDER_STATUS_ID);
        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_ORDER_STATUS_ID');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_ZONE')) {
        $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_PS_ZONE', MODULE_PAYMENT_PAYPAL_STANDARD_ZONE);
        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_ZONE');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_SORT_ORDER')) {
        $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_PS_SORT_ORDER', MODULE_PAYMENT_PAYPAL_STANDARD_SORT_ORDER, 'Sort Order', 'Sort order of display (lowest to highest).');
        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_SORT_ORDER');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_TRANSACTIONS_ORDER_STATUS_ID')) {
        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_TRANSACTIONS_ORDER_STATUS_ID');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_STATUS')) {
        $status = '-1';

        if ((MODULE_PAYMENT_PAYPAL_STANDARD_STATUS == 'True') && defined('MODULE_PAYMENT_PAYPAL_STANDARD_GATEWAY_SERVER')) {
          if (MODULE_PAYMENT_PAYPAL_STANDARD_GATEWAY_SERVER == 'Live') {
            $status = '1';
          } else {
            $status = '0';
          }
        }

        $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_PS_STATUS', $status);
        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_STATUS');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_GATEWAY_SERVER')) {
        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_GATEWAY_SERVER');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_VERIFY_SSL')) {
        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_VERIFY_SSL');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_PROXY')) {
        if (!empty(MODULE_PAYMENT_PAYPAL_STANDARD_PROXY) && empty(CLICSHOPPING_HTTP_PROXY)) {
          $this->app->saveCfgParam('CLICSHOPPING_HTTP_PROXY', MODULE_PAYMENT_PAYPAL_STANDARD_PROXY);
        }

        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_PROXY');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_DEBUG_EMAIL')) {
        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_DEBUG_EMAIL');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_EWP_STATUS')) {
        if (!defined('CLICSHOPPING_APP_PAYPAL_PS_EWP_STATUS')) {
          $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_PS_EWP_STATUS', (MODULE_PAYMENT_PAYPAL_STANDARD_EWP_STATUS == 'True') ? '1' : '-1');
        }

        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_EWP_STATUS');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_EWP_PRIVATE_KEY')) {
        if (!defined('CLICSHOPPING_APP_PAYPAL_PS_EWP_PRIVATE_KEY')) {
          $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_PS_EWP_PRIVATE_KEY', MODULE_PAYMENT_PAYPAL_STANDARD_EWP_PRIVATE_KEY);
        }

        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_EWP_PRIVATE_KEY');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_EWP_PUBLIC_KEY')) {
        if (!defined('CLICSHOPPING_APP_PAYPAL_PS_EWP_PUBLIC_CERT')) {
          $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_PS_EWP_PUBLIC_CERT', MODULE_PAYMENT_PAYPAL_STANDARD_EWP_PUBLIC_KEY);
        }

        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_EWP_PUBLIC_KEY');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_EWP_CERT_ID')) {
        if (!defined('CLICSHOPPING_APP_PAYPAL_PS_EWP_PUBLIC_CERT_ID')) {
          $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_PS_EWP_PUBLIC_CERT_ID', MODULE_PAYMENT_PAYPAL_STANDARD_EWP_CERT_ID);
        }

        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_EWP_CERT_ID');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_EWP_PAYPAL_KEY')) {
        if (!defined('CLICSHOPPING_APP_PAYPAL_PS_EWP_PAYPAL_CERT')) {
          $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_PS_EWP_PAYPAL_CERT', MODULE_PAYMENT_PAYPAL_STANDARD_EWP_PAYPAL_KEY);
        }

        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_EWP_PAYPAL_KEY');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_EWP_WORKING_DIRECTORY')) {
        if (!defined('CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY')) {
          $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY', MODULE_PAYMENT_PAYPAL_STANDARD_EWP_WORKING_DIRECTORY);
        }

        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_EWP_WORKING_DIRECTORY');
      }

      if (defined('MODULE_PAYMENT_PAYPAL_STANDARD_EWP_OPENSSL')) {
        if (!defined('CLICSHOPPING_APP_PAYPAL_PS_EWP_OPENSSL')) {
          $this->app->saveCfgParam('CLICSHOPPING_APP_PAYPAL_PS_EWP_OPENSSL', MODULE_PAYMENT_PAYPAL_STANDARD_EWP_OPENSSL);
        }

        $this->app->deleteCfgParam('MODULE_PAYMENT_PAYPAL_STANDARD_EWP_OPENSSL');
      }
    }
  }
