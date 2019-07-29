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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;
  
  class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Currency');
    }

    public function execute()
    {
      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

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