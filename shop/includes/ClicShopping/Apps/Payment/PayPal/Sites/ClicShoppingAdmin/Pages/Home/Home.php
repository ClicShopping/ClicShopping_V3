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

  namespace ClicShopping\Apps\Payment\PayPal\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Apps;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  use ClicShopping\OM\Cache;

  use ClicShopping\Apps\Payment\PayPal\PayPal;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public $app;

    protected function init()
    {
      $CLICSHOPPING_PayPal = new PayPal();
      Registry::set('PayPal', $CLICSHOPPING_PayPal);

      $this->app = $CLICSHOPPING_PayPal;

      $this->app->loadDefinitions('ClicShoppingAdmin');
      $this->app->loadDefinitions('ClicShoppingAdmin/start');

      if ($this->app->migrate()) {
        $admin_dashboard_modules = explode(';', MODULE_ADMIN_DASHBOARD_INSTALLED);

        foreach (Apps::getModules('adminDashboard', 'PayPal') as $k => $v) {
          if (!in_array($k, $admin_dashboard_modules)) {
            $admin_dashboard_modules[] = $k;

            $adm = new $v();
            $adm->install();
          }
        }

        if (isset($adm)) {
          $this->app->db->save('configuration', [
            'configuration_value' => implode(';', $admin_dashboard_modules)
          ], [
              'configuration_key' => 'MODULE_ADMIN_DASHBOARD_INSTALLED'
            ]
          );
        }

        CLICSHOPPING::redirect(null, CLICSHOPPING::getAllGET());
      }

      if (!$this->isActionRequest()) {
        $paypal_menu_check = [
          'CLICSHOPPING_APP_PAYPAL_LIVE_SELLER_EMAIL',
          'CLICSHOPPING_APP_PAYPAL_LIVE_API_USERNAME',
          'CLICSHOPPING_APP_PAYPAL_SANDBOX_SELLER_EMAIL',
          'CLICSHOPPING_APP_PAYPAL_SANDBOX_API_USERNAME',
          'CLICSHOPPING_APP_PAYPAL_PF_LIVE_VENDOR',
          'CLICSHOPPING_APP_PAYPAL_PF_SANDBOX_VENDOR'
        ];

        foreach ($paypal_menu_check as $value) {
          if (defined($value) && !empty(constant($value))) {
            $this->runAction('Configure');
            break;
          }
        }
      }
    }


    public function getFile2()
    {
      if (isset($this->file)) {
        return __DIR__ . '/templates/' . $this->file;
      }
    }
  }
