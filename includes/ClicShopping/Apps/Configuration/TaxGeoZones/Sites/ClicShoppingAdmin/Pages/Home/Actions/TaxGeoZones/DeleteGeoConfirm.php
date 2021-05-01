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


  namespace ClicShopping\Apps\Configuration\TaxGeoZones\Sites\ClicShoppingAdmin\Pages\Home\Actions\TaxGeoZones;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  class DeleteGeoConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('TaxGeoZones');
    }

    public function execute()
    {
      $page = (isset($_GET['spage']) && is_numeric($_GET['spage'])) ? $_GET['spage'] : 1;
      $sID = HTML::sanitize($_GET['sID']);
      $zpage = HTML::sanitize($_GET['zpage']);
      $zID = HTML::sanitize($_GET['zID']);


      $this->app->db->delete('zones_to_geo_zones', ['association_id' => (int)$sID]);

      $this->app->redirect('ListGeo&zpage=' . $zpage . '&zID=' . $zID . '&spage=' .$page);
    }
  }