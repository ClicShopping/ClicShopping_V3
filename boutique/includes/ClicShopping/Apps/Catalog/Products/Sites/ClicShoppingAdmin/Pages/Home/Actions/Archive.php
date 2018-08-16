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
  use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\General;

  class Archive extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_Products = Registry::get('Products');

      $CLICSHOPPING_ProductsGeneral = new General();
      Registry::set('ProductsGeneral', $CLICSHOPPING_ProductsGeneral);

      $this->page->setFile('archive.php');
      $this->page->data['action'] = 'ArchiveConfirm';

      $CLICSHOPPING_Products->loadDefinitions('Sites/ClicShoppingAdmin/Products');
    }
  }