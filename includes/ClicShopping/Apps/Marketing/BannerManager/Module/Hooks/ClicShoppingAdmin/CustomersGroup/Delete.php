<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\BannerManager\Module\Hooks\ClicShoppingAdmin\CustomersGroup;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Marketing\BannerManager\BannerManager as BannerManagerApp;

class Delete implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('BannerManager')) {
      Registry::set('BannerManager', new BannerManagerApp());
    }

    $this->app = Registry::get('BannerManager');
  }

  /**
   * @param int $group_id
   */
  private function delete(int $group_id): void
  {
    $QbannerCustomersId = $this->app->db->prepare('select count(customers_group_id) as count
                                                     from :table_banners
                                                     where customers_group_id = :customers_group_id
                                                   ');
    $QbannerCustomersId->bindInt(':customers_group_id', (int)$group_id);
    $QbannerCustomersId->execute();

    $banner_customers_id = $QbannerCustomersId->fetch();

    if ($banner_customers_id['count'] > 0) {
      $Qdelete = $this->app->db->prepare('delete from :table_banners
                                                        where customers_group_id = :customers_group_id
                                                       ');
      $Qdelete->bindInt(':customers_group_id', (int)$group_id);
      $Qdelete->execute();
    }
  }

  public function execute()
  {
    if (isset($_GET['Delete'])) {
      $id = HTML::sanitize($_GET['cID']);
      $this->delete($id);
    }
  }
}