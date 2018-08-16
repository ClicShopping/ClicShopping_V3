<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  namespace ClicShopping\Apps\Configuration\TaxGeoZones\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class ListGeo extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_TaxGeoZones = Registry::get('TaxGeoZones');

      $this->page->setFile('list_geo.php');
      $this->page->data['action'] = 'ListGeo';

      $CLICSHOPPING_TaxGeoZones->loadDefinitions('Sites/ClicShoppingAdmin/TaxGeoZones');
    }
  }