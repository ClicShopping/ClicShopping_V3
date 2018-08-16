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

  namespace ClicShopping\Apps\Configuration\Langues\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class delete extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_Langues = Registry::get('Langues');

      $this->page->setFile('delete.php');
      $this->page->data['action'] = 'Delete';

      $CLICSHOPPING_Langues->loadDefinitions('Sites/ClicShoppingAdmin/Langues');
    }
  }