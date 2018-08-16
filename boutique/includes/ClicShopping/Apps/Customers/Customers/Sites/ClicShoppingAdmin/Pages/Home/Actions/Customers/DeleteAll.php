<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */


  namespace ClicShopping\Apps\Customers\Customers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Customers;

  use ClicShopping\OM\Registry;

  class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_Customers = Registry::get('Customers');

      if (!empty($_POST['selected'])) {
        foreach ($_POST['selected'] as $id ) {

          $CLICSHOPPING_Customers->db->delete('address_book', ['customers_id' => $id]);
          $CLICSHOPPING_Customers->db->delete('customers', ['customers_id' => $id]);
          $CLICSHOPPING_Customers->db->delete('customers_info', ['customers_info_id' => $id]);
          $CLICSHOPPING_Customers->db->delete('customers_basket', ['customers_id' => $id]);
          $CLICSHOPPING_Customers->db->delete('customers_basket_attributes', ['customers_id' => $id]);
          $CLICSHOPPING_Customers->db->delete('whos_online', ['customer_id' => $id]);

          if (isset($_POST['delete_reviews']) && ($_POST['delete_reviews'] == 'on')) {

            $Qreviews = $CLICSHOPPING_Customers->db->get('reviews', 'reviews_id', ['customers_id' => $id]);

            while ($Qreviews->fetch()) {
              $CLICSHOPPING_Customers->db->delete('reviews_description', ['reviews_id' => (int)$Qreviews->valueInt('reviews_id')]);
            }

            $CLICSHOPPING_Customers->db->delete('reviews', ['customers_id' => $id]);
          } else {
            $CLICSHOPPING_Customers->db->save('reviews', ['customers_id' => 'null'], ['customers_id' => $id]);
          }
        }
      }

      $CLICSHOPPING_Customers->redirect('Customers', 'page=' . $_GET['page']);
    }
  }