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

  namespace ClicShopping\Apps\Configuration\Administrators\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class Insert extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_Administrators = Registry::get('Administrators');

      $this->page->setFile('insert.php');
      $this->page->data['action'] = 'Insert';

      $CLICSHOPPING_Administrators->loadDefinitions('Sites/ClicShoppingAdmin/Administrators');
    }
  }