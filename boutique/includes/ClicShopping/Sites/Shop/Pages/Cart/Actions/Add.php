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

  class Add extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');
      $CLICSHOPPING_Prod = Registry::get('Prod');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if ( isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken']) ) {

        if ( isset($_POST['products_id']) && is_numeric($_POST['products_id']) ) {
          $attributes = isset($_POST['id']) ? $_POST['id'] : '';

          $CLICSHOPPING_ShoppingCart->addCart($_POST['products_id'], $CLICSHOPPING_ShoppingCart->get_quantity($CLICSHOPPING_Prod::getProductIDString($_POST['products_id'], $attributes))+($_POST['cart_quantity']), $attributes);
        }

        if (DISPLAY_CART == 'true' ) {
          $goto =  'index.php';
          $parameters = 'Cart';
        } else {
          $goto = 'index.php';

          if (isset($_POST['url'])) {
            $parameters = $_POST['url'];
          }
        }

        $CLICSHOPPING_Hooks->call('Cart', 'Add');

        CLICSHOPPING::redirect($goto, $parameters);
      }
    }
  }
