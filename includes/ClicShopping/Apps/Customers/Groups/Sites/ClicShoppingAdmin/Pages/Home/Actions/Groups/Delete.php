<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Groups\Sites\ClicShoppingAdmin\Pages\Home\Actions\Groups;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Delete extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Groups = Registry::get('Groups');
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    if (isset($_GET['cID'])) {
      $group_id = HTML::sanitize($_GET['cID']);
    } else {
      $group_id = null;
    }

    if (!\is_null($group_id)) {
      $Qdelete = $CLICSHOPPING_Groups->db->prepare('delete
                                                      from :table_groups_to_categories
                                                      where customers_group_id = :customers_group_id
                                                    ');
      $Qdelete->bindInt(':customers_group_id', (int)$group_id);
      $Qdelete->execute();

      $Qdelete = $CLICSHOPPING_Groups->db->prepare('delete
                                                      from :table_customers_groups
                                                      where customers_group_id = :customers_group_id
                                                    ');
      $Qdelete->bindInt(':customers_group_id', (int)$group_id);
      $Qdelete->execute();

      $Qdelete = $CLICSHOPPING_Groups->db->prepare('delete
                                                      from :table_products_groups
                                                      where customers_group_id = :customers_group_id
                                                    ');
      $Qdelete->bindInt(':customers_group_id', (int)$group_id);
      $Qdelete->execute();

      $CLICSHOPPING_Hooks->call('CustomersGroup', 'Delete');
    }

    $CLICSHOPPING_Groups->redirect('Groups');
  }
}