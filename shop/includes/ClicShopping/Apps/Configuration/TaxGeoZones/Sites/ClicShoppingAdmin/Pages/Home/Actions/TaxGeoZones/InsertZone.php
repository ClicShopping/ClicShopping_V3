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

  class InsertZone extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $CLICSHOPPING_TaxGeoZones = Registry::get('TaxGeoZones');
      $this->app = $CLICSHOPPING_TaxGeoZones;
    }

    public function execute() {

      $geo_zone_name = HTML::sanitize($_POST['geo_zone_name']);
      $geo_zone_description = HTML::sanitize($_POST['geo_zone_description']);

      $this->app->db->save('geo_zones', [
                                          'geo_zone_name' => $geo_zone_name,
                                          'geo_zone_description' =>  $geo_zone_description,
                                          'date_added' => 'now()'
                                          ]
                          );

      $new_zone_id = $this->app->db->lastInsertId();

      $this->app->redirect('TaxGeoZones&zpage=' . $_GET['zpage'] . '&zID=' . $new_zone_id);
    }
  }