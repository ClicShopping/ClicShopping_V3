<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Tools\Apps\Sites\ClicShoppingAdmin\Pages\Home\Actions\Apps;

use ClicShopping\OM\Apps;
use ClicShopping\OM\Registry;

class getInstalledApps extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('Apps');
  }

  public function execute()
  {

    if (isset($_GET['action'])) {
      if ($_GET['action'] == '1') {
        $result = [
          'result' => -1
        ];

        $apps = Apps::getAll();

        if (\is_array($apps)) {
          $result['result'] = 1;
          $result['apps'] = $apps;
        }

        echo json_encode($result);
        exit;
      }
    }
  }
}