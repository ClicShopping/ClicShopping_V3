<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Communication\Newsletter\Sites\ClicShoppingAdmin\Pages\Home\Actions\Newsletter;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Lock extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Newsletter = Registry::get('Newsletter');

    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
    $newsletter_id = null;

    if (isset($_GET['nID'])) {
      $newsletter_id = HTML::sanitize($_GET['nID']);
    }

    $status = 1;

    $CLICSHOPPING_Newsletter->db->save('newsletters', ['locked' => $status], ['newsletters_id' => (int)$newsletter_id]);

    $CLICSHOPPING_Newsletter->redirect('Newsletter&page=' . $page . '&nID=' . $newsletter_id);

  }
}