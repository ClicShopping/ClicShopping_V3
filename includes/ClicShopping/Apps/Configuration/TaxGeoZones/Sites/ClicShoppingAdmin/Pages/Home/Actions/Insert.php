<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TaxGeoZones\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class Insert extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_TaxGeoZones = Registry::get('TaxGeoZones');

    $this->page->setFile('insert.php');
    $this->page->data['action'] = 'Insert';

    $CLICSHOPPING_TaxGeoZones->loadDefinitions('Sites/ClicShoppingAdmin/TaxGeoZones');
  }
}