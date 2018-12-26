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

  class InsertCategories extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_Groups = Registry::get('Groups');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      $group_id = HTML::sanitize($_POST['cID']);

      if ((empty($_POST['discount'])) || ($_POST['categories_id']) == 0) {
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Groups->getDef('entry_groups_categorie_error'));

        $CLICSHOPPING_Groups->redirect('Edit&cID=' . $group_id);

      } else {

        $new_category_discount = HTML::sanitize($_POST['discount']);
        $new_category_id = HTML::sanitize($_POST['categories_id']);

        $new_category_discount = round($new_category_discount,2);

        $Qcheck = $CLICSHOPPING_Groups->db->prepare('select *
                                               from :table_groups_to_categories
                                               where customers_group_id = :customers_group_id
                                               and categories_id = :categories_id
                                             ');
        $Qcheck->bindInt(':customers_group_id', (int)$group_id );
        $Qcheck->bindInt(':categories_id', (int)$new_category_id );
        $Qcheck->execute();

        if ($Qcheck->fetch() === false) {

          $CLICSHOPPING_Groups->db->save('groups_to_categories', [
                                                  'customers_group_id' =>  (int)$group_id,
                                                  'categories_id' => (int)$new_category_id,
                                                  'discount' => (float)$new_category_discount
                                                  ]
                          );
        }

        $CLICSHOPPING_Hooks->call('CustomersGroup','InsertCategories');

        $CLICSHOPPING_Groups->redirect('Edit&cID=' . $group_id . '#tab4');
      }
    }
  }