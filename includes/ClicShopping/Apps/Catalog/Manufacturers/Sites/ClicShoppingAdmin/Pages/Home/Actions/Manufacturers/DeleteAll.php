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

  namespace ClicShopping\Apps\Catalog\Manufacturers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Manufacturers;

  use ClicShopping\OM\Registry;

  class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function execute()
    {
      $this->app = Registry::get('Manufacturers');

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
        }
      }

      $this->app->redirect('Manufacturers');
    }
  }