<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TaxGeoZones\Sites\ClicShoppingAdmin\Pages\Home\Actions\TaxGeoZones;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Update extends \ClicShopping\OM\PagesActionsAbstract
{
  protected mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('TaxGeoZones');
  }

  public function execute()
  {
    $page = (isset($_GET['zpage']) && is_numeric($_GET['zpage'])) ? $_GET['zpage'] : 1;
    $zID = HTML::sanitize($_GET['zID']);
    $geo_zone_name = HTML::sanitize($_POST['geo_zone_name']);
    $geo_zone_description = HTML::sanitize($_POST['geo_zone_description']);

    $this->app->db->save('geo_zones', [
      'geo_zone_name' => $geo_zone_name,
      'geo_zone_description' => $geo_zone_description,
      'last_modified' => 'now()'
    ], [
        'geo_zone_id' => (int)$zID
      ]
    );

    $this->app->redirect('TaxGeoZones&zpage=' . $page . '&zID=' . $zID);
  }
}