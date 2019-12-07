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

  namespace ClicShopping\Apps\Configuration\Zones\Sites\ClicShoppingAdmin\Pages\Home\Actions\Zones;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\Zones\Classes\ClicShoppingAdmin\Status;

  class AllFlag extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Zones');
    }

    public function execute()
    {
      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

       if (!empty($_POST['selected'])) {
        foreach ($_POST['selected'] as $id) {

          $Qzones = $this->app->db->prepare('select zone_status
                                            from :table_zones
                                            where zone_id = :zone_id
                                           ');

          $Qzones->bindInt(':zone_id', $id);
          $Qzones->execute();


          if ($Qzones->valueInt('zone_status') == 1) {
            Status::getZonesStatus($id, 0);
          } else {
            Status::getZonesStatus($id, 1);
          }
        }
      }

      $this->app->redirect('Zones&page=' . $page);
    }
  }