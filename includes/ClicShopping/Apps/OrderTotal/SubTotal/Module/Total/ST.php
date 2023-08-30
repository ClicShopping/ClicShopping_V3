<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\OrderTotal\SubTotal\Module\Total;

use ClicShopping\OM\Registry;
use ClicShopping\Apps\OrderTotal\SubTotal\SubTotal as SubTotalApp;

class ST implements \ClicShopping\OM\Modules\OrderTotalInterface
{
  public string $code;
  public $title;
  public $description;
  public $enabled;
  public $group;
  public $output;
  public ?int $sort_order = 0;
  public mixed $app;
  public $surcharge;
  public $maximum;
  public $signature;
  protected $api_version;
  public $public_title;

  public function __construct()
  {
    if (!Registry::exists('SubTotal')) {
      Registry::set('SubTotal', new SubTotalApp());
    }

    $this->app = Registry::get('SubTotal');
    $this->app->loadDefinitions('Module/Shop/ST/ST');

    $this->signature = 'Tax|' . $this->app->getVersion() . '|1.0';
    $this->api_version = $this->app->getApiVersion();

    $this->code = 'ST';
    $this->title = $this->app->getDef('module_st_title');
    $this->public_title = $this->app->getDef('module_st_public_title');

    $this->enabled = \defined('CLICSHOPPING_APP_ORDER_TOTAL_SUBTOTAL_ST_STATUS') && (CLICSHOPPING_APP_ORDER_TOTAL_SUBTOTAL_ST_STATUS == 'True') ? true : false;

    $this->sort_order = \defined('CLICSHOPPING_APP_ORDER_TOTAL_SUBTOTAL_ST_SORT_ORDER') && ((int)CLICSHOPPING_APP_ORDER_TOTAL_SUBTOTAL_ST_SORT_ORDER > 0) ? (int)CLICSHOPPING_APP_ORDER_TOTAL_SUBTOTAL_ST_SORT_ORDER : 0;

    $this->output = [];
  }

  public function process()
  {
    $CLICSHOPPING_Currencies = Registry::get('Currencies');
    $CLICSHOPPING_Order = Registry::get('Order');

    $this->output[] = [
      'title' => $this->title,
      'text' => $CLICSHOPPING_Currencies->format($CLICSHOPPING_Order->info['subtotal'], true, $CLICSHOPPING_Order->info['currency'], $CLICSHOPPING_Order->info['currency_value']),
      'value' => $CLICSHOPPING_Order->info['subtotal']
    ];
  }

  public function check()
  {
    return \defined('CLICSHOPPING_APP_ORDER_TOTAL_SUBTOTAL_ST_STATUS') && (trim(CLICSHOPPING_APP_ORDER_TOTAL_SUBTOTAL_ST_STATUS) != '');
  }

  public function install()
  {
    $this->app->redirect('Configure&Install&module=ST');
  }

  public function remove()
  {
    $this->app->redirect('Configure&Uninstall&module=ST');
  }

  public function keys()
  {
    return array('CLICSHOPPING_APP_ORDER_TOTAL_SUBTOTAL_ST_SORT_ORDER');
  }
}
