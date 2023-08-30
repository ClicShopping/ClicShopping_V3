<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Suppliers\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class SuppliersPopUp extends \ClicShopping\OM\PagesActionsAbstract
{

  public function execute()
  {
    $CLICSHOPPING_Suppliers = Registry::get('Suppliers');

    $this->page->setUseSiteTemplate(false); //don't display Header / Footer
    $this->page->setFile('suppliers_popup.php');
    $this->page->data['action'] = 'SuppliersPopUp';

    $CLICSHOPPING_Suppliers->loadDefinitions('Sites/ClicShoppingAdmin/suppliers_popup');
  }
}