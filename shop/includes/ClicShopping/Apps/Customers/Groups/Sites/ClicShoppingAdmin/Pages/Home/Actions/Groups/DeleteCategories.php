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

  namespace ClicShopping\Apps\Customers\Groups\Sites\ClicShoppingAdmin\Pages\Home\Actions\Groups;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class DeleteCategories extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_Groups = Registry::get('Groups');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $category_id = HTML::sanitize($_POST['catID']);
      $customers_groups_id = HTML::sanitize($_POST['customers_groups_id']);

      $Qdelete = $CLICSHOPPING_Groups->db->prepare('delete
                                             from :table_groups_to_categories
                                             where customers_group_id = :customers_group_id
                                             and categories_id = :categories_id
                                            ');
      $Qdelete->bindInt(':customers_group_id', (int)$customers_groups_id);
      $Qdelete->bindInt(':categories_id', (int)$category_id);
      $Qdelete->execute();

      $CLICSHOPPING_Hooks->call('CustomersGroup','DeleteCategories');

      $CLICSHOPPING_Groups->redirect('Edit&cID=' . $customers_groups_id . '#tab4');
    }
  }