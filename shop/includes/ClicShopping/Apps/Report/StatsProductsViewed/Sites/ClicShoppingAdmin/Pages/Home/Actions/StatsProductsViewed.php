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

  namespace ClicShopping\Apps\Report\StatsProductsViewed\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class StatsProductsViewed extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_StatsProductsViewed = Registry::get('StatsProductsViewed');

      $this->page->setFile('stats_products_viewed.php');

      $CLICSHOPPING_StatsProductsViewed->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }