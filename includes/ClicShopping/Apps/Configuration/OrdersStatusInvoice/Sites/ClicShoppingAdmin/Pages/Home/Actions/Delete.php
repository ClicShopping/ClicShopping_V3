<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\OrdersStatusInvoice\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class delete extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_OrdersStatusInvoice = Registry::get('OrdersStatusInvoice');

    $this->page->setFile('delete.php');
    $this->page->data['action'] = 'Delete';

    $CLICSHOPPING_OrdersStatusInvoice->loadDefinitions('Sites/ClicShoppingAdmin/OrdersStatusInvoice');
  }
}