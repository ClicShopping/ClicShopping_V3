<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\Newsletter\Sites\ClicShoppingAdmin\Pages\Home\Actions;


use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class ConfirmSend extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Newsletter = Registry::get('Newsletter');
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

    $this->page->setFile('confirm_send.php');

    $newsletter_id = HTML::sanitize($_GET['nID']);

    $Qcheck = $CLICSHOPPING_Newsletter->db->get('newsletters', 'locked', ['newsletters_id' => (int)$newsletter_id]);

    if ($Qcheck->fetch() !== false) {
      if ($Qcheck->valueInt('locked') < 1) {
        $error = $CLICSHOPPING_Newsletter->getDef('error_remove_unlocked_newsletter');

        $CLICSHOPPING_MessageStack->add($error, 'error');

        $CLICSHOPPING_Newsletter->redirect('Newsletter&page=' . (int)$_GET['page'] . '&nID=' . (int)$_GET['nID']);
      }
    }

    $CLICSHOPPING_Newsletter->loadDefinitions('Sites/ClicShoppingAdmin/Newsletter');
  }
}