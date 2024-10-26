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
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\Currency\Classes\ClicShoppingAdmin\CurrenciesAdmin;

class UpdateAll extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;
  private mixed $db;

  public function __construct()
  {
    $this->app = Registry::get('Currency');
  }

  public function execute()
  {
    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

    if (!Registry::exists('CurrenciesAdmin')) {
      Registry::set('CurrenciesAdmin', new CurrenciesAdmin());
    }

    $CurrenciesAdmin = Registry::get('CurrenciesAdmin');

    $CurrenciesAdmin->updateAllCurrencies();

    Cache::clear('currencies');

    $this->app->redirect('Currency&page=' . $page);
  }
}