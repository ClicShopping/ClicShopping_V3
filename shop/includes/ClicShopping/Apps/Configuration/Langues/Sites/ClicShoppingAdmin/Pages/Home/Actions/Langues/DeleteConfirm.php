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


  namespace ClicShopping\Apps\Configuration\Langues\Sites\ClicShoppingAdmin\Pages\Home\Actions\Langues;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Langues');
    }

    public function execute()
    {
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
      $lID = HTML::sanitize($_GET['lID']);

      $Qlng = $this->app->db->get('languages', 'languages_id', ['code' => DEFAULT_LANGUAGE]);

      if ($Qlng->valueInt('languages_id') === (int)$lID) {
        $this->app->db->save('configuration', ['configuration_value' => ''],
          ['configuration_key' => 'DEFAULT_CURRENCY']
        );
      }

// Delete all table for the language deleted
      $this->app->db->delete('languages', ['languages_id' => $lID]);


      $this->app->db->delete('products_options', ['language_id' => $lID]);
      $this->app->db->delete('products_options_values', ['language_id' => $lID]);

      $CLICSHOPPING_Hooks->call('Langues', 'DeleteConfirm');

      Cache::clear('languages-system-shop');
      Cache::clear('languages-system-admin');

      $this->app->redirect('Langues&page=' . $page);
    }
  }