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

  namespace ClicShopping\Apps\Marketing\Featured\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class Edit extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_Featured = Registry::get('Featured');

      $this->page->setFile('edit.php');
      $this->page->data['action'] = 'Edit';

      $CLICSHOPPING_Featured->loadDefinitions('Sites/ClicShoppingAdmin/Featured');
    }
  }