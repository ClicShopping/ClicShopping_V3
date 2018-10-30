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

  namespace ClicShopping\Apps\Tools\ServiceAPP\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class ServiceAPP extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_ServiceAPP = Registry::get('ServiceAPP');

      $this->page->setFile('service.php');

      $CLICSHOPPING_ServiceAPP->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }