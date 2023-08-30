<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Manufacturers\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class ManufacturersPopUp extends \ClicShopping\OM\PagesActionsAbstract
{

  protected $use_site_template = false;

  public function execute()
  {
    $CLICSHOPPING_Manufacturers = Registry::get('Manufacturers');

    $this->page->setUseSiteTemplate(false); //don't display Header / Footer
    $this->page->setFile('manufacturers_popup.php');
    $this->page->data['action'] = 'ManufacturersPopUp';

    $CLICSHOPPING_Manufacturers->loadDefinitions('Sites/ClicShoppingAdmin/manufacturers_popup');
  }
}