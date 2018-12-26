<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Configuration\TaxGeoZones\Sites\ClicShoppingAdmin\Pages\Home\Actions\TaxGeoZones;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  class InsertGeoZone extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('TaxGeoZones');
    }

    public function execute() {

      $zID = HTML::sanitize($_GET['zID']);
      $zone_country_id = HTML::sanitize($_POST['country']);
      $zone_id = HTML::sanitize($_POST['state']);

      $this->app->db->save('zones_to_geo_zones', [
                                                  'zone_country_id' =>  (int)$zone_country_id,
                                                  'zone_id' => (int)$zone_id,
                                                  'geo_zone_id' =>  (int)$zID,
                                                  'date_added' => 'now()'
                                                 ]
                          );

      $new_subzone_id = $this->app->db->lastInsertId();

      $this->app->redirect('ListGeo&zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&spage=' . $_GET['spage'] . '&sID=' . $new_subzone_id);
    }
  }

  include(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'ext/javascript/clicshopping/ClicShoppingAdmin/state_dropdown.php');