<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Groups\Module\Hooks\ClicShoppingAdmin\Customers;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Customers\Groups\Groups as GroupsApp;

class Update implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Groups')) {
      Registry::set('Groups', new GroupsApp());
    }

    $this->app = Registry::get('Groups');
  }

  public function execute()
  {
    if (isset($_GET['Update'], $_GET['Customers'])) {
      $CLICSHOPPING_Customers = Registry::get('Customers');

      if (isset($_POST['customers_group_id'])) {
        if (isset($_POST['customers_id'])) {
          $customers_group_id = HTML::sanitize($_POST['customers_group_id']);
          if (empty($customers_group_id)) $customers_group_id = 0;

          $customers_id = HTML::sanitize($_POST['customers_id']);

          $QmultipleGroups = $CLICSHOPPING_Customers->db->prepare('select distinct customers_group_id
                                                                     from :table_products_groups
                                                                   ');

          $QmultipleGroups->execute();

          while ($QmultipleGroups->fetch()) {
            $QmultipleCustomers = $CLICSHOPPING_Customers->db->prepare('select distinct customers_group_id
                                                                          from :table_customers_groups
                                                                          where customers_group_id = :customers_group_id
                                                                        ');
            $QmultipleCustomers->bindInt(':customers_group_id', $QmultipleGroups->valueInt('customers_group_id'));
            $QmultipleCustomers->execute();

            if (!($QmultipleCustomers->fetch())) {
              $Qdelete = $CLICSHOPPING_Customers->db->prepare('delete
                                                                from :table_products_groups
                                                                where customers_group_id = :customers_group_id
                                                               ');
              $Qdelete->bindInt(':customers_group_id', $QmultipleGroups->valueInt('customers_group_id'));

              $Qdelete->execute();
            }
          } // end while

          $sql_data_array = ['customers_group_id' => $customers_group_id];

          $this->app->db->save('customers', $sql_data_array, ['customers_id' => (int)$customers_id]);
        }
      }
    }
  }
}