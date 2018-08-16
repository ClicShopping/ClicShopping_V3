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

  namespace ClicShopping\Apps\Configuration\TaxRates\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class delete extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_TaxRates = Registry::get('TaxRates');

      $this->page->setFile('delete.php');
      $this->page->data['action'] = 'Delete';

      $CLICSHOPPING_TaxRates->loadDefinitions('Sites/ClicShoppingAdmin/TaxRates');
    }
  }