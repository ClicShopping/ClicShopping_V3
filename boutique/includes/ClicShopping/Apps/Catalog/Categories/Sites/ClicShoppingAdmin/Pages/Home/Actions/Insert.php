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

  namespace ClicShopping\Apps\Catalog\Categories\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class Insert extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_Categories = Registry::get('Categories');
      $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');

      $this->page->setFile('insert.php');
      $this->page->data['action'] = 'Insert';

      $CLICSHOPPING_Categories->loadDefinitions('Sites/ClicShoppingAdmin/Categories');
    }
  }