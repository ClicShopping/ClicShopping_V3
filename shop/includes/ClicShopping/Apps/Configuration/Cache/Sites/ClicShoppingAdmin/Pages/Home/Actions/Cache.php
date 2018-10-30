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

  namespace ClicShopping\Apps\Configuration\Cache\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class Cache extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_Cache = Registry::get('Cache');

      $this->page->setFile('cache.php');
      $this->page->data['action'] = 'Cache';

      $CLICSHOPPING_Cache->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }