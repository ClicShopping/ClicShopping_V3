<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Configuration\Currency\Sites\ClicShoppingAdmin\Pages\Home\Actions\Currency;

use ClicShopping\OM\Cache;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('Currency');
  }

  public function execute()
  {
    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

    $currencies_id = HTML::sanitize($_GET['cID']);

    $Qcurrency = $this->app->db->get('currencies', 'currencies_id', ['code' => DEFAULT_CURRENCY]);

    if ($Qcurrency->valueInt('currencies_id') === (int)$currencies_id) {
      $this->app->db->save('configuration', ['configuration_value' => ''], ['configuration_key' => 'DEFAULT_CURRENCY']);
    }

    $this->app->db->delete('currencies', ['currencies_id' => (int)$currencies_id]);

    Cache::clear('currencies');

    $this->app->redirect('Currency&&page=' . $page);
  }
}