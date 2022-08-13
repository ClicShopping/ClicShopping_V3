<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  namespace ClicShopping\Apps\Configuration\TaxGeoZones\Sites\ClicShoppingAdmin\Pages\Home\Actions\TaxGeoZones;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  class UpdateGeoZone extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function __construct()
    {
      $this->app = Registry::get('TaxGeoZones');
    }

    public function execute()
    {
      $zpage = (isset($_GET['zpage']) && is_numeric($_GET['zpage'])) ? $_GET['zpage'] : 1;
      $spage = (isset($_GET['spage']) && is_numeric($_GET['spage'])) ? $_GET['spage'] : 1;

      $sID = HTML::sanitize($_GET['sID']);
      $zID = HTML::sanitize($_GET['zID']);
      $zone_country_id = HTML::sanitize($_POST['zone_country_id']);
      $zone_id = HTML::sanitize($_POST['zone_id']);

      $this->app->db->save('zones_to_geo_zones', [
        'geo_zone_id' => (int)$zID,
        'zone_country_id' => (int)$zone_country_id,
        'zone_id' => (!empty($zone_id) ? (int)$zone_id : 'null'),
        'last_modified' => 'now()'
      ], [
          'association_id' => (int)$sID
        ]
      );

      $this->app->redirect('ListGeo&zpage=' . $zpage . '&zID=' . $zID . '&spage=' . $spage . '&sID=' . $sID);
    }
  }