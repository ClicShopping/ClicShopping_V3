<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Tools\Cronjob\Sites\ClicShoppingAdmin\Pages\Home\Actions\Cronjob;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Delete extends \ClicShopping\OM\PagesActionsAbstract
{
  private int $cronId;

  public function __construct()
  {
    $this->app = Registry::get('Cronjob');
    $this->cronId = HTML::sanitize($_GET['cronId']);
  }

  public function execute()
  {
    if (isset($_GET['Delete'])) {
      $this->app->db->delete('cron', ['cron_id' => (int)$this->cronId]);
    }

    $this->app->redirect('Cronjob');
  }
}