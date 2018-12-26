<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


  namespace ClicShopping\Apps\Communication\Newsletter\Sites\ClicShoppingAdmin\Pages\Home\Actions\Newsletter;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;


  class Unlock extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_Newsletter = Registry::get('Newsletter');

      $newsletter_id = HTML::sanitize($_GET['nID']);
      $status = 0;

      $CLICSHOPPING_Newsletter->db->save('newsletters', ['locked' => $status], ['newsletters_id' => (int)$newsletter_id]);

      $CLICSHOPPING_Newsletter->redirect('Newsletter&page=' . $_GET['page'] . '&nID=' . $_GET['nID']);

    }
  }