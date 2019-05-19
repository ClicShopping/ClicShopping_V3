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

  namespace ClicShopping\Apps\OrderTotal\TotalShipping\Module\Total;

  use ClicShopping\OM\Registry;

  use ClicShopping\Sites\Common\B2BCommon;

  use ClicShopping\Apps\OrderTotal\TotalShipping\TotalShipping as TotalShippingApp;


  class SH implements \ClicShopping\OM\Modules\OrderTotalInterface
  {

    public $code;
    public $title;
    public $description;
    public $enabled;
    public $output;
    public $sort_order = 0;
    public $app;
    public $surcharge;
    public $maximum;

    public function __construct()
    {

      if (!Registry::exists('TotalShipping')) {
        Registry::set('TotalShipping', new TotalShippingApp());
      }

      $this->app = Registry::get('TotalShipping');
      $this->app->loadDefinitions('Module/Shop/SH/SH');

      $this->signature = 'Total Shipping |' . $this->app->getVersion() . '|1.0';
      $this->api_version = $this->app->getApiVersion();

      $this->code = 'SH';
      $this->title = $this->app->getDef('module_sh_title');
      $this->public_title = $this->app->getDef('module_sh_public_title');

      $this->enabled = defined('CLICSHOPPING_APP_ORDER_TOTAL_SHIPPING_SH_STATUS') && (CLICSHOPPING_APP_ORDER_TOTAL_SHIPPING_SH_STATUS == 'True') ? true : false;

      $this->sort_order = defined('CLICSHOPPING_APP_ORDER_TOTAL_SHIPPING_SH_SORT_ORDER') && ((int)CLICSHOPPING_APP_ORDER_TOTAL_SHIPPING_SH_SORT_ORDER > 0) ? (int)CLICSHOPPING_APP_ORDER_TOTAL_SHIPPING_SH_SORT_ORDER : 0;

      $this->output = [];
    }

    public function process()
    {

      $CLICSHOPPING_Currencies = Registry::get('Currencies');
      $CLICSHOPPING_Order = Registry::get('Order');
      $CLICSHOPPING_Tax = Registry::get('Tax');

      if (defined('CLICSHOPPING_APP_ORDER_TOTAL_SHIPPING_SH_OVER')) {
        if (CLICSHOPPING_APP_ORDER_TOTAL_SHIPPING_SH_OVER == 'True') {
          $pass = false;
          switch (CLICSHOPPING_APP_ORDER_TOTAL_SHIPPING_SH_DESTINATION) {
            case 'national':
              if ($CLICSHOPPING_Order->delivery['country_id'] == STORE_COUNTRY) $pass = true;
              break;
            case 'international':
              if ($CLICSHOPPING_Order->delivery['country_id'] != STORE_COUNTRY) $pass = true;
              break;
            case 'both':
              $pass = true;
              break;
            default:
              $pass = false;
              break;
          }

          if (($pass === true) && (($CLICSHOPPING_Order->info['total'] - $CLICSHOPPING_Order->info['shipping_cost']) >= CLICSHOPPING_APP_ORDER_TOTAL_SHIPPING_SH_OVER)) {
            $CLICSHOPPING_Order->info['shipping_method'] = CLICSHOPPING::getDef('free_shipping_title');
            $CLICSHOPPING_Order->info['total'] -= $CLICSHOPPING_Order->info['shipping_cost'];
            $CLICSHOPPING_Order->info['shipping_cost'] = 0;
          }
        }
      }

      if (isset($_SESSION['shipping']) && strpos($_SESSION['shipping']['id'], '\\') !== false) {
        list($vendor, $app, $module) = explode('\\', $_SESSION['shipping']['id']);
        list($module, $method) = explode('_', $module);

        $module = $vendor . '\\' . $app . '\\' . $module;

        $code = 'Shipping_' . str_replace('\\', '_', $module);

        if (Registry::exists($code)) {
          $CLICSHOPPING_SM = Registry::get($code);
        }
      }

      if (!is_null($CLICSHOPPING_Order->info['shipping_method'])) {
        if (isset($CLICSHOPPING_SM->tax_class) && $CLICSHOPPING_SM->tax_class > 0) {
          $shipping_tax = $CLICSHOPPING_Tax->getTaxRate($CLICSHOPPING_SM->tax_class, $CLICSHOPPING_Order->delivery['country']['id'], $CLICSHOPPING_Order->delivery['zone_id']);
          $shipping_tax_description = $CLICSHOPPING_Tax->getTaxRateDescription($CLICSHOPPING_SM->tax_class, $CLICSHOPPING_Order->delivery['country']['id'], $CLICSHOPPING_Order->delivery['zone_id']);
          $CLICSHOPPING_Order->info['tax'] += $CLICSHOPPING_Tax->calculate($CLICSHOPPING_Order->info['shipping_cost'], $shipping_tax);

          if (isset($CLICSHOPPING_Order->info['tax_groups']["$shipping_tax_description"])) {
            $CLICSHOPPING_Order->info['tax_groups']["$shipping_tax_description"] += $CLICSHOPPING_Tax->calculate($CLICSHOPPING_Order->info['shipping_cost'], $shipping_tax);
          } else {
            $CLICSHOPPING_Order->info['tax_groups']["$shipping_tax_description"] = $CLICSHOPPING_Tax->calculate($CLICSHOPPING_Order->info['shipping_cost'], $shipping_tax);
          }

          $CLICSHOPPING_Order->info['total'] += $CLICSHOPPING_Tax->calculate($CLICSHOPPING_Order->info['shipping_cost'], $shipping_tax);

          if (DISPLAY_PRICE_WITH_TAX == 'True') $CLICSHOPPING_Order->info['shipping_cost'] += $CLICSHOPPING_Tax->calculate($CLICSHOPPING_Order->info['shipping_cost'], $shipping_tax);
        }

        $this->output[] = ['title' => $CLICSHOPPING_Order->info['shipping_method'],
          'text' => $CLICSHOPPING_Currencies->format($CLICSHOPPING_Order->info['shipping_cost'], true, $CLICSHOPPING_Order->info['currency'], $CLICSHOPPING_Order->info['currency_value']),
          'value' => $CLICSHOPPING_Order->info['shipping_cost']
        ];
      }
    }


    public function check()
    {
      return defined('CLICSHOPPING_APP_ORDER_TOTAL_SHIPPING_SH_STATUS') && (trim(CLICSHOPPING_APP_ORDER_TOTAL_SHIPPING_SH_STATUS) != '');
    }

    public function install()
    {
      $this->app->redirect('Configure&Install&module=SH');
    }

    public function remove()
    {
      $this->app->redirect('Configure&Uninstall&module=SH');
    }

    public function keys()
    {
      return array('CLICSHOPPING_APP_ORDER_TOTAL_SHIPPING_SH_SORT_ORDER');
    }
  }
