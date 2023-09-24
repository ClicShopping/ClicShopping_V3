<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\TaxRates\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class Insert extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_TaxRates = Registry::get('TaxRates');

    $this->page->setFile('insert.php');
    $this->page->data['action'] = 'Insert';

    $CLICSHOPPING_TaxRates->loadDefinitions('Sites/ClicShoppingAdmin/TaxRates');
  }
}