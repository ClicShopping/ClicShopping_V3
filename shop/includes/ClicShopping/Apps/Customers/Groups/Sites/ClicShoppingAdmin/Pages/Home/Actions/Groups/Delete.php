<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Customers\Groups\Sites\ClicShoppingAdmin\Pages\Home\Actions\Groups;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Delete extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Groups = Registry::get('Groups');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (isset($_GET['cID'])) $group_id = HTML::sanitize($_GET['cID']);

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

// delete all banners
      $QbannerCustomersId = $CLICSHOPPING_Groups->db->prepare('select count(customers_group_id) as count
                                                               from :table_banners
                                                               where customers_group_id = :customers_group_id
                                                             ');
      $QbannerCustomersId->bindInt(':customers_group_id', (int)$group_id);
      $QbannerCustomersId->execute();

      $banner_customers_id = $QbannerCustomersId->fetch();

      if ($banner_customers_id['count'] > 0) {

        $Qdelete = $CLICSHOPPING_Groups->db->prepare('delete from :table_banners
                                                where customers_group_id = :customers_group_id
                                               ');
        $Qdelete->bindInt(':customers_group_id', (int)$group_id);
        $Qdelete->execute();
      }

// delete all extra fields

      $QproductsExtraFieldsCustomersId = $CLICSHOPPING_Groups->db->prepare('select count(customers_group_id) as count
                                                                           from :table_products_extra_fields
                                                                           where customers_group_id = :customers_group_id
                                                                          ');
      $QproductsExtraFieldsCustomersId->bindInt(':customers_group_id', (int)$group_id);
      $QproductsExtraFieldsCustomersId->execute();

      if ($QproductsExtraFieldsCustomersId->valueInt('count') > 0) {

        $Qdelete = $CLICSHOPPING_Groups->db->prepare('delete
                                                      from :table_products_extra_fields
                                                      where customers_group_id = :customers_group_id
                                                    ');
        $Qdelete->bindInt(':customers_group_id', (int)$group_id);
        $Qdelete->execute();
      }

      $QpageManagerCustomersId = $CLICSHOPPING_Groups->db->prepare("select count(customers_group_id) as count
                                                       from :table_pages_manager
                                                       where customers_group_id = :customers_group_id
                                                     ");
      $QpageManagerCustomersId->bindInt(':customers_group_id', (int)$group_id);
      $QpageManagerCustomersId->execute();

      if ($QpageManagerCustomersId->valueInt('count') > 0) {
// delete all page manager
        $Qdelete = $CLICSHOPPING_Groups->db->prepare('delete
                                                      from :table_pages_manager
                                                      where customers_group_id = :customers_group_id
                                                    ');
        $Qdelete->bindInt(':customers_group_id', (int)$group_id);
        $Qdelete->execute();
      }

// delete all specials
      $QspecialsProductsCustomersId = $CLICSHOPPING_Groups->db->prepare('select count(customers_group_id) as count
                                                                         from :table_specials
                                                                         where customers_group_id = :customers_group_id
                                                                        ');
      $QspecialsProductsCustomersId->bindInt(':customers_group_id', (int)$group_id);
      $QspecialsProductsCustomersId->execute();

      if ($QspecialsProductsCustomersId->valueInt('count') > 0) {

        $Qdelete = $CLICSHOPPING_Groups->db->prepare('delete
                                                      from :table_specials
                                                      where customers_group_id = :customers_group_id
                                                    ');
        $Qdelete->bindInt(':customers_group_id', (int)$group_id);
        $Qdelete->execute();
      }

// delete all products favorites
      $QProductsFavoritesCustomersId = $CLICSHOPPING_Groups->db->prepare('select count(customers_group_id) as count
                                                               from :table_products_favorites
                                                               where customers_group_id = :customers_group_id
                                                             ');
      $QProductsFavoritesCustomersId->bindInt(':customers_group_id', (int)$group_id);
      $QProductsFavoritesCustomersId->execute();

      if ($QProductsFavoritesCustomersId->valueInt('count') > 0) {

        $Qdelete = $CLICSHOPPING_Groups->db->prepare('delete
                                                from :table_products_favorites
                                                where customers_group_id = :customers_group_id
                                              ');
        $Qdelete->bindInt(':customers_group_id', (int)$group_id);
        $Qdelete->execute();
      }
// delete all newsletter

      $QnewsletteCustomersId = $CLICSHOPPING_Groups->db->prepare('select count(customers_group_id) as count
                                                                   from :table_newsletters
                                                                   where customers_group_id = :customers_group_id
                                                                 ');
      $QnewsletteCustomersId->bindInt(':customers_group_id', (int)$group_id);
      $QnewsletteCustomersId->execute();

      if ($QnewsletteCustomersId->valueInt('count') > 0) {

        $Qdelete = $CLICSHOPPING_Groups->db->prepare('delete
                                                      from :table_newsletters
                                                      where customers_group_id = :customers_group_id
                                                    ');
        $Qdelete->bindInt(':customers_group_id', (int)$group_id);
        $Qdelete->execute();
      }

// update all customers
      $QcustomersId = $CLICSHOPPING_Groups->db->prepare('select customers_id
                                                         from :table_customers
                                                         where customers_group_id = :customers_group_id
                                                       ');
      $QcustomersId->bindInt(':customers_group_id', (int)$group_id);
      $QcustomersId->execute();

      while ($QcustomersId->fetch()) {

        $Qupdate = $CLICSHOPPING_Groups->db->prepare('update :table_customers
                                                      set customers_group_id = :customers_group_id
                                                      where customers_id = :customers_id
                                                      ');
        $Qupdate->bindValue(':customers_group_id', 1);
        $Qupdate->bindInt(':customers_id', $QcustomersId->valueInt('customers_id'));
        $Qupdate->execute();
      }

      $CLICSHOPPING_Hooks->call('CustomersGroup', 'Delete');

      $CLICSHOPPING_Groups->redirect('Groups');
    }
  }