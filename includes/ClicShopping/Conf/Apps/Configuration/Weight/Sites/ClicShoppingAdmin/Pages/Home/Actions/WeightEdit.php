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

class WeightEdit extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Weight = Registry::get('Weight');

    $this->page->setFile('weight_edit.php');
    $this->page->data['action'] = 'WeightUpdate';

    $CLICSHOPPING_Weight->loadDefinitions('Sites/ClicShoppingAdmin/weight');
  }
}