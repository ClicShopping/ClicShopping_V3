<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */


  namespace ClicShopping\Apps\Configuration\TaxRates\Sites\ClicShoppingAdmin\Pages\Home\Actions\TaxRates;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('TaxRates');
    }

    public function execute() {

      $tax_rates_id = HTML::sanitize($_GET['tID']);

      $this->app->db->delete('tax_rates', ['tax_rates_id' => (int)$tax_rates_id]);

      $this->app->redirect('TaxRates&'. (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : ''));
    }
  }