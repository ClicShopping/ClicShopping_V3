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

  namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class CategoriesPopUp extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      $CLICSHOPPING_Products = Registry::get('Products');
      $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');

      $this->page->setUseSiteTemplate(false); //don't display Header / Footer
      $this->page->setFile('categories_popup.php');
      $this->page->data['action'] = 'CategoriesPopUp';

      $CLICSHOPPING_Products->loadDefinitions('Sites/ClicShoppingAdmin/categories_popup');
    }
  }