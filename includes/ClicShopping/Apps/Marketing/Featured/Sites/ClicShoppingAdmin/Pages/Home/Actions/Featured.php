<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Featured\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class Featured extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Featured = Registry::get('Featured');

    $this->page->setFile('featured.php');
    $this->page->data['action'] = 'Featured';

    $CLICSHOPPING_Featured->loadDefinitions('Sites/ClicShoppingAdmin/Featured');
  }
}