<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Configuration\TaxClass\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class TaxClass extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_TaxClass = Registry::get('TaxClass');

      $this->page->setFile('tax_class.php');
      $this->page->data['action'] = 'TaxClass';

      $CLICSHOPPING_TaxClass->loadDefinitions('Sites/ClicShoppingAdmin/TaxClass');
    }
  }