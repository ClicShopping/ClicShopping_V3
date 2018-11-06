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

  namespace ClicShopping\Sites\Shop\Pages\Cart\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class Update extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (isset($_GET['Update']) && isset($_GET['Cart'])) {
        if (isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
          if (isset($_POST['products_id'])) {

            for ($i=0, $n=count($_POST['products_id']); $i<$n; $i++) {

              $attributes = isset($_POST['id'][$_POST['products_id'][$i]]) ? $_POST['id'][$_POST['products_id'][$i]] : '';
              $product_id = $CLICSHOPPING_ShoppingCart->getUprid($_POST['products_id'][$i], $attributes);

              if ($_POST['cart_quantity'][$i] > 0) {

                echo $_POST['products_id'][$i] . ' - ' . $_POST['cart_quantity'][$i];

                if ($_POST['cart_quantity'][$i] != $CLICSHOPPING_ShoppingCart->getQuantity($CLICSHOPPING_ShoppingCart->getUprid($_POST['products_id'][$i], $attributes))) {
                  $CLICSHOPPING_ShoppingCart->addCart($_POST['products_id'][$i], $_POST['cart_quantity'][$i], $attributes, false);
                }
              } else{
                if (in_array($_POST['products_id'][$i], (is_array($_POST['cart_delete']) ? $_POST['cart_delete'] : array()))) {
                  $CLICSHOPPING_ShoppingCart->remove($product_id);
                }
              }
            }
          }

          $CLICSHOPPING_Hooks->call('Cart', 'Update');
        }
      }

      CLICSHOPPING::redirect(null, 'Cart');
    }
  }
