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

  namespace ClicShopping\Apps\Tools\WhosOnline\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class WhosOnline extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_WhosOnline = Registry::get('WhosOnline');

      $this->page->setFile('whos_online.php');

      $CLICSHOPPING_WhosOnline->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }