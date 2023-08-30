<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\OrdersStatusInvoice\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class Insert extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_OrdersStatusInvoice = Registry::get('OrdersStatusInvoice');

    $this->page->setFile('insert.php');
    $this->page->data['action'] = 'Insert';

    $CLICSHOPPING_OrdersStatusInvoice->loadDefinitions('Sites/ClicShoppingAdmin/OrdersStatusInvoice');
  }
}