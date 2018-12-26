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


  namespace ClicShopping\Apps\Configuration\ProductsQuantityUnit\Sites\ClicShoppingAdmin\Pages\Home\Actions\ProductsQuantityUnit;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('ProductsQuantityUnit');
    }

    public function execute() {

      $oID = HTML::sanitize($_GET['oID']);

      $Qstatus = $this->app->db->get('configuration', 'configuration_value', ['configuration_key' => 'DEFAULT_PRODUCTS_QUANTITY_UNIT_STATUS_ID']);

      if ($Qstatus->value('configuration_value') == $oID) {
        $this->app->db->save('configuration', [
                                                'configuration_value' => ''
                                              ], [
                                                  'configuration_key' => 'DEFAULT_PRODUCTS_QUANTITY_UNIT_STATUS_ID'
                                                ]
                            );
      }

      $this->app->db->delete('products_quantity_unit', ['products_quantity_unit_id' => (int)$oID]);

      $this->app->redirect('ProductsQuantityUnit&'. (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : ''));
    }
  }