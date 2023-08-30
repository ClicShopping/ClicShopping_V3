<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Cronjob\Sites\ClicShoppingAdmin\Pages\Home\Actions\Cronjob;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Tools\Cronjob\Classes\ClicShoppingAdmin\Cron;

class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
{
  public function __construct()
  {
    $this->app = Registry::get('Cronjob');
  }

  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

    Cron::getCronjobStatus($_GET['id'], $_GET['flag']);

    $CLICSHOPPING_MessageStack->add($this->app->getDef('success_cronjob_status_updated'), 'success');

    $this->app->redirect('Cronjob');
  }
}