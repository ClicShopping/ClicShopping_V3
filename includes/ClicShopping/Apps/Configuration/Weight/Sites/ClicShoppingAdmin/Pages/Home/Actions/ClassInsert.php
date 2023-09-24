<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Weight\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class ClassInsert extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Weight = Registry::get('Weight');

    $this->page->setFile('class_insert.php');
    $this->page->data['action'] = 'ClassInsert';

    $CLICSHOPPING_Weight->loadDefinitions('Sites/ClicShoppingAdmin/weight');
  }
}