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

  namespace ClicShopping\Apps\Communication\PageManager\Sites\Shop\Pages\RSS;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Communication\PageManager\PageManager as PageManagerApp;

  class RSS extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init()  {

      if (!Registry::exists('PageManager')) {
        Registry::set('PageManager', new PageManagerApp());
      }

      $CLICSHOPPING_PageManager = Registry::get('PageManager');
      $this->app = $CLICSHOPPING_PageManager;

      $CLICSHOPPING_PageManager->loadDefinitions('Sites/Shop/main');
    }
  }
