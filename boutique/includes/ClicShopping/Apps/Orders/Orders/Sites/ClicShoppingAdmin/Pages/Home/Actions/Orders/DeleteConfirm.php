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


  namespace ClicShopping\Apps\Orders\Orders\Sites\ClicShoppingAdmin\Pages\Home\Actions\Orders;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Orders\Orders\Classes\ClicShoppingAdmin\OrderAdmin;

  class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;
    protected $restock;
    protected $oID;

    public function __construct() {
      $this->app = Registry::get('Orders');
      $this->restock = $_POST['restock'];
      $this->oID = HTML::sanitize($_GET['oID']);
    }

    public function execute() {
      $CLICSHOPPING_MessageStack =  Registry::get('MessageStack');

     if ($this->oID != 0) {
       OrderAdmin::removeOrder($this->oID, $this->restock);
     } else {
       $CLICSHOPPING_MessageStack->add($this->app->getDef('warning_order_not_updated'), 'warning');
     }

      $this->app->redirect('Orders');
    }
  }