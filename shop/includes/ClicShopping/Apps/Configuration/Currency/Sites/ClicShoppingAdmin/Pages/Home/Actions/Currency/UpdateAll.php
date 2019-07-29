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

  namespace ClicShopping\Apps\Configuration\Currency\Sites\ClicShoppingAdmin\Pages\Home\Actions\Currency;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Cache;

  use ClicShopping\Apps\Configuration\Currency\Classes\ClicShoppingAdmin\CurrenciesAdmin;

  class UpdateAll extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;
    protected $db;

    public function __construct()
    {
      $this->app = Registry::get('Currency');
      $this->db = Registry::get('Db');
    }

    public function getConvertCurrency()
    {

      $CLICSHOPPING_CurrenciesAdmin = new CurrenciesAdmin();

      $XML = HTTP::getResponse([
        'url' => 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml'
      ]);

      if (empty($XML)) {
        throw new \Exception('Can not load currency rates from the European Central Bank website');
      }

      $currencies = [];

      foreach ($CLICSHOPPING_CurrenciesAdmin->getAll() as $c) {
        $currencies[$c['code']] = null;
      }

      $XML = new \SimpleXMLElement($XML);

      foreach ($XML->Cube->Cube->Cube as $rate) {
        if (array_key_exists((string)$rate['currency'], $currencies)) {
          $currencies[(string)$rate['currency']] = (float)$rate['rate'];
        }
      }

      foreach ($currencies as $code => $value) {
        if (!is_null($value)) {
          try {
            $this->db->save('currencies', [
              'value' => $value,
              'last_updated' => 'now()'
            ], [
              'code' => $code
            ]);
          } catch (\PDOException $e) {
            trigger_error($e->getMessage());
          }
        }
      }

      Cache::clear('currencies');
    }

    public function execute()
    {
      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

      $this->getConvertCurrency();

      $this->app->redirect('Currency&page=' . $page);
    }
  }