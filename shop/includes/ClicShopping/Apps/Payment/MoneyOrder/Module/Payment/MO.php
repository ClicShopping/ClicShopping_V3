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

  namespace ClicShopping\Apps\Payment\MoneyOrder\Module\Payment;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Payment\MoneyOrder\MoneyOrder as MoneyOrderApp;
  use ClicShopping\Sites\Common\B2BCommon;


  class MO implements \ClicShopping\OM\Modules\PaymentInterface
  {

    public $code;
    public $title;
    public $description;
    public $enabled;
    public $app;
    public $title_selection;

    public function __construct()
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if (Registry::exists('Order')) {
        $CLICSHOPPING_Order = Registry::get('Order');
      }

      if (!Registry::exists('MoneyOrder')) {
        Registry::set('MoneyOrder', new MoneyOrderApp());
      }

      $this->app = Registry::get('MoneyOrder');
      $this->app->loadDefinitions('Module/Shop/MO/MO');


      $this->signature = 'MoneyOrder|' . $this->app->getVersion() . '|1.0';
      $this->api_version = $this->app->getApiVersion();

      $this->code = 'MO';
      $this->title = $this->app->getDef('module_moneyorder_title');
      $this->public_title = $this->app->getDef('module_moneyorder_public_title');

// Activation module du paiement selon les groupes B2B
      if (defined('CLICSHOPPING_APP_MONEYORDER_MO_STATUS')) {
        if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
          if (B2BCommon::getPaymentUnallowed($this->code)) {
            if (CLICSHOPPING_APP_MONEYORDER_MO_STATUS == 'True') {
              $this->enabled = true;
            } else {
              $this->enabled = false;
            }
          }
        } else {
          if (CLICSHOPPING_APP_MONEYORDER_MO_NO_AUTHORIZE == 'True' && $CLICSHOPPING_Customer->getCustomersGroupID() == 0) {
            if ($CLICSHOPPING_Customer->getCustomersGroupID() == 0) {
              if (CLICSHOPPING_APP_MONEYORDER_MO_STATUS == 'True') {
                $this->enabled = true;
              } else {
                $this->enabled = false;
              }
            }
          }
        }

        if ((int)CLICSHOPPING_APP_MONEYORDER_MO_PREPARE_ORDER_STATUS_ID > 0) {
          $this->order_status = CLICSHOPPING_APP_MONEYORDER_MO_PREPARE_ORDER_STATUS_ID;
        }

        if ($this->enabled === true) {
          if (isset($CLICSHOPPING_Order) && is_object($CLICSHOPPING_Order)) {
            $this->update_status();
          }
        }

        $this->sort_order = defined('CLICSHOPPING_APP_MONEYORDER_MO_SORT_ORDER') ? CLICSHOPPING_APP_MONEYORDER_MO_SORT_ORDER : 0;
      }
    }


    public function update_status()
    {
      $CLICSHOPPING_Order = Registry::get('Order');

      if (($this->enabled === true) && ((int)CLICSHOPPING_APP_MONEYORDER_MO_ZONE > 0)) {
        $check_flag = false;

        $Qcheck = $this->app->db->get('zones_to_geo_zones', 'zone_id', ['geo_zone_id' => CLICSHOPPING_APP_MONEYORDER_MO_ZONE,
          'zone_country_id' => $CLICSHOPPING_Order->delivery['country']['id']
        ],
          'zone_id'
        );

        while ($Qcheck->fetch()) {
          if (($Qcheck->valueInt('zone_id') < 1) || ($Qcheck->valueInt('zone_id') == $CLICSHOPPING_Order->delivery['zone_id'])) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag === false) {
          $this->enabled = false;
        }
      }
    }

    public function javascript_validation()
    {
      return false;
    }

    public function selection()
    {
      $CLICSHOPPING_Template = Registry::get('Template');

      if (CLICSHOPPING_APP_MONEYORDER_MO_LOGO) {
        if (!empty(CLICSHOPPING_APP_MONEYORDER_MO_LOGO) && is_file($CLICSHOPPING_Template->getDirectoryTemplateImages() . 'logos/payment/' . CLICSHOPPING_APP_MONEYORDER_MO_LOGO)) {
          $this->public_title = $this->public_title . '&nbsp;&nbsp;&nbsp;' . HTML::image($CLICSHOPPING_Template->getDirectoryTemplateImages() . 'logos/payment/' . CLICSHOPPING_APP_MONEYORDER_MO_LOGO);
        } else {
          $this->public_title = $this->public_title;
        }
      }

      return ['id' => $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code,
        'module' => $this->public_title
      ];
    }

    public function pre_confirmation_check()
    {
      return false;
    }

    public function confirmation()
    {
      $CLICSHOPPING_Template = Registry::get('Template');
      $this->title_selection = '';

      if (defined('CLICSHOPPING_APP_MONEYORDER_MO_LOGO') && !empty(CLICSHOPPING_APP_MONEYORDER_MO_LOGO)) {
        $this->title_selection .= HTML::image($CLICSHOPPING_Template->getDirectoryTemplateImages() . 'logos/payment/' . CLICSHOPPING_APP_MONEYORDER_MO_LOGO);
      }

      $this->title_selection .= '<br />' . $this->app->getDef('module_moneyorder_text', ['pay_to' => CLICSHOPPING_APP_MONEYORDER_MO_PAY_TO,
            'store_name_addres' => STORE_NAME_ADDRESS,
            'store_name' => STORE_NAME]
        );

      return array('title' => $this->title_selection);
    }

    public function process_button()
    {
      return false;
    }

    public function before_process()
    {
      return false;
    }

    public function after_process()
    {
      return false;
    }

    public function get_error()
    {
      return false;
    }


    public function check()
    {
      return defined('CLICSHOPPING_APP_MONEYORDER_MO_STATUS') && (trim(CLICSHOPPING_APP_MONEYORDER_MO_STATUS) != '');
    }

    public function install()
    {
      $this->app->redirect('Configure&Install&module=MoneyOrder');
    }

    public function remove()
    {
      $this->app->redirect('Configure&Uninstall&module=MoneyOrder');
    }

    public function keys()
    {
      return array('CLICSHOPPING_APP_MONEYORDER_MO_SORT_ORDER');
    }

  }