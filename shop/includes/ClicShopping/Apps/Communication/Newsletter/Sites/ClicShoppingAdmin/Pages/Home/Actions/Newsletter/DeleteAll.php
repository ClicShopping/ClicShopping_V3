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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_Newsletter = Registry::get('Newsletter');

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

      if (isset($_GET['nID'])) $nID = HTML::sanitize($_GET['nID']);

      if (!empty($_POST['selected'])) {
        foreach ($_POST['selected'] as $id) {

          $Qdelete = $CLICSHOPPING_Newsletter->db->prepare('delete
                                                      from :table_newsletters
                                                      where newsletters_id = :newsletters_id
                                                    ');
          $Qdelete->bindInt(':newsletters_id', $id);
          $Qdelete->execute();
        }
      }

      $CLICSHOPPING_Newsletter->redirect('Newsletter&page=' . $page  . '&nID=' . $nID);
    }
  }