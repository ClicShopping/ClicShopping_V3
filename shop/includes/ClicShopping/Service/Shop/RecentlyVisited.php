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

  namespace ClicShopping\Service\Shop;

  use ClicShopping\OM\Registry;

  use ClicShopping\Sites\Shop\RecentlyVisited as RecentlyVisitedClass;

  class RecentlyVisited implements \ClicShopping\OM\ServiceInterface {

    public static function start() {
      $CLICSHOPPING_Service = Registry::get('Service');
      Registry::set('RecentlyVisited', new RecentlyVisitedClass());

      $CLICSHOPPING_Service->addCallBeforePageContent('RecentlyVisited', 'initialize');

      return true;
    }

    public static function stop() {
      return true;
    }
  }