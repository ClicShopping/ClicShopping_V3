<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Cronjob\Sites\ClicShoppingAdmin\Pages\Home\Actions;

use ClicShopping\OM\Registry;

class Cronjob extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Cronjob = Registry::get('Cronjob');

    $this->page->setFile('cronjob.php');
    $this->page->data['action'] = 'Cronjob';

    $CLICSHOPPING_Cronjob->loadDefinitions('Sites/ClicShoppingAdmin/Cronjob');
  }
}