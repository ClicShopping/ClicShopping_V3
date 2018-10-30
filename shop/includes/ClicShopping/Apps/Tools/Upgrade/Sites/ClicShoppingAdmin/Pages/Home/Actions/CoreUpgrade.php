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

  namespace ClicShopping\Apps\Tools\Upgrade\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class CoreUpgrade extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_Upgrade = Registry::get('Upgrade');

      $this->page->setFile('core_upgrade.php');
      $this->page->data['action'] = 'CoreUpgrade';

      $CLICSHOPPING_Upgrade->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }