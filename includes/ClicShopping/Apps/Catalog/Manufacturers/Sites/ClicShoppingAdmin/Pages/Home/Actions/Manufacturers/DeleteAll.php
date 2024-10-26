<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Manufacturers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Manufacturers;

use ClicShopping\OM\Registry;

class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;

  public function execute()
  {
    $this->app = Registry::get('Manufacturers');
    $this->Hooks = Registry::get('Hooks');

    if (isset($_POST['selected'])) {
      foreach ($_POST['selected'] as $id) {

        $this->app->db->delete('manufacturers', ['manufacturers_id' => (int)$id]);
        $this->app->db->delete('manufacturers_info', ['manufacturers_id' => (int)$id]);

        $Qupdate = $this->app->db->prepare('update :table_products
                                              set products_status = 0,
                                                  manufacturers_id = :manufacturers_id
                                              where manufacturers_id = :manufacturers_id
                                            ');

        $Qupdate->bindInt(':manufacturers_id', '');
        $Qupdate->bindInt(':manufacturers_id', $id);

        $Qupdate->execute();

        $this->Hooks->call('Manufacturers', 'Delete');
      }
    }

    $this->app->redirect('Manufacturers');
  }
}