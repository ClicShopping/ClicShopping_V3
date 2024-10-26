<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Upgrade\Sites\ClicShoppingAdmin\Pages\Home\Actions\CoreUpgrade;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\FileSystem;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Tools\Upgrade\Classes\ClicShoppingAdmin\Github;

class Process extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('Upgrade');
  }

  public function execute()
  {

    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_Github = new Github();

    $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');

    if (FileSystem::isWritable(CLICSHOPPING::BASE_DIR . 'Work/Temp/')) {
      $CLICSHOPPING_Github->upgradeClicShoppingCore();
    } else {
      $CLICSHOPPING_MessageStack->add($this->app->getDef('error_directory_not_writable'), 'warning', 'header');
    }
  }
}