<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\Newsletter\Sites\ClicShoppingAdmin\Pages\Home\Actions;


use ClicShopping\OM\Registry;

class Update extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Newsletter = Registry::get('Newsletter');

    $this->page->setFile('update.php');
    $this->page->data['action'] = 'Update';

    $CLICSHOPPING_Newsletter->loadDefinitions('Sites/ClicShoppingAdmin/Newsletter');
  }
}