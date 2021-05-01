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

  class Unpack extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;
    protected $oID;

    public function __construct()
    {
      $this->app = Registry::get('Orders');
      $this->oID = HTML::sanitize($_GET['oID']);
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if ($this->oID != 0) {
        $Qupdate = $this->app->db->prepare('update :table_orders
                                            set orders_archive = 0
                                            where orders_id = :orders_id
                                          ');

        $Qupdate->bindInt(':orders_id', $this->oID);
        $Qupdate->execute();
      } else {
        $CLICSHOPPING_MessageStack->add($this->app->getDef('warning_order_not_updated'), 'warning');
      }

      $this->app->redirect('Orders');
    }
  }