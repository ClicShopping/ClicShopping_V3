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

  namespace ClicShopping\Apps\OrderTotal\Total\Module\Total;

  use ClicShopping\OM\Registry;

  use ClicShopping\Sites\Common\B2BCommon;

  use ClicShopping\Apps\OrderTotal\Total\Total as TotalApp;


  class TO implements \ClicShopping\OM\Modules\OrderTotalInterface  {

    public $code;
    public $title;
    public $description;
    public $enabled;
    public $output;
    public $sort_order = 0;
    public $app;

    public function __construct() {

      if (!Registry::exists('Total')) {
        Registry::set('Total', new TotalApp());
      }

      $this->app = Registry::get('Total');
      $this->app->loadDefinitions('Module/Shop/TO/TO');

      $this->signature = 'Total|' . $this->app->getVersion() . '|1.0';
      $this->api_version = $this->app->getApiVersion();

      $this->code = 'TO';
      $this->title = $this->app->getDef('module_to_title');
      $this->public_title = $this->app->getDef('module_to_public_title');

      $this->enabled = defined('CLICSHOPPING_APP_ORDER_TOTAL_TOTAL_TO_STATUS') && (CLICSHOPPING_APP_ORDER_TOTAL_TOTAL_TO_STATUS == 'True') ? true : false;

      $this->sort_order = defined('CLICSHOPPING_APP_ORDER_TOTAL_TOTAL_TO_SORT_ORDER') && ((int)CLICSHOPPING_APP_ORDER_TOTAL_TOTAL_TO_SORT_ORDER > 0) ? (int)CLICSHOPPING_APP_ORDER_TOTAL_TOTAL_TO_SORT_ORDER : 0;

      $this->output = [];
    }

    public function process() {

      $CLICSHOPPING_Currencies = Registry::get('Currencies');
      $CLICSHOPPING_Order = Registry::get('Order');

      $this->output[] = ['title' => $this->title,
                          'text' => ' ' . $CLICSHOPPING_Currencies->format($CLICSHOPPING_Order->info['total'], true, $CLICSHOPPING_Order->info['currency'], $CLICSHOPPING_Order->info['currency_value']) . ' ',
                          'value' => $CLICSHOPPING_Order->info['total']
                        ];
    }



    public function check() {
      return defined('CLICSHOPPING_APP_ORDER_TOTAL_TOTAL_TO_STATUS') && (trim(CLICSHOPPING_APP_ORDER_TOTAL_TOTAL_TO_STATUS) != '');
    }

    public function install() {
      $this->app->redirect('Configure&Install&module=TO');
    }

    public function remove() {
      $this->app->redirect('Configure&Uninstall&module=TO');
    }

    public function keys() {
      return array('CLICSHOPPING_APP_ORDER_TOTAL_TOTAL_TO_SORT_ORDER');
    }
  }
