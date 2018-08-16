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

  namespace ClicShopping\Apps\Configuration\OrdersStatus\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class Insert extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_OrdersStatus = Registry::get('OrdersStatus');

      $this->page->setFile('insert.php');
      $this->page->data['action'] = 'Insert';

      $CLICSHOPPING_OrdersStatus->loadDefinitions('Sites/ClicShoppingAdmin/OrdersStatus');
    }
  }