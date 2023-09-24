<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Suppliers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Suppliers;

use ClicShopping\OM\Registry;

class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract
{
  protected mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('Suppliers');
    $this->Hooks = Registry::get('Hooks');
  }

  public function execute()
  {
    if (isset($_POST['selected'])) {
      foreach ($_POST['selected'] as $id) {
        $Qdelete = $this->app->db->prepare('delete
                                              from :table_suppliers
                                              where suppliers_id = :suppliers_id
                                            ');
        $Qdelete->bindInt(':suppliers_id', $id);
        $Qdelete->execute();

        $Qdelete = $this->app->db->prepare('delete
                                              from :table_suppliers_info
                                              where suppliers_id = :suppliers_id
                                            ');
        $Qdelete->bindInt(':suppliers_id', $id);
        $Qdelete->execute();

        $Qupdate = $this->app->db->prepare('update :table_products
                                              set suppliers_id = :suppliers_id,
                                                  products_status = 0
                                              where suppliers_id = :suppliers_id1
                                            ');
        $Qupdate->bindInt(':suppliers_id', '');
        $Qupdate->bindInt(':suppliers_id1', $id);

        $Qupdate->execute();

        $this->Hooks->call('Suppliers', 'Delete');
      }
    }

    $this->app->redirect('Suppliers');
  }
}