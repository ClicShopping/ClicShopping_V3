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


  namespace ClicShopping\Apps\Orders\Orders\Sites\ClicShoppingAdmin\Pages\Home\Actions\Orders;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Orders\Orders\Classes\ClicShoppingAdmin\OrderAdmin;

  class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;
    protected bool $restock;
    protected int $oID;

    public function __construct()
    {
      $this->app = Registry::get('Orders');
      $this->oID = HTML::sanitize($_GET['oID']);
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if ($this->oID != 0) {
        if(isset($_POST['restock'])) {
          $restock = true;
        } else {
          $restock = false;
        }

        OrderAdmin::removeOrder($this->oID, $restock);
      } else {
        $CLICSHOPPING_MessageStack->add($this->app->getDef('warning_order_not_updated'), 'warning');
      }

      $CLICSHOPPING_Hooks->call('Orders', 'DeleteConfirm');

      $this->app->redirect('Orders');
    }
  }