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

  namespace ClicShopping\Sites\Shop\Pages\Cart\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class delete extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (isset($_GET['products_id'])) {
        $CLICSHOPPING_ShoppingCart->remove($_GET['products_id']);
      }

      $CLICSHOPPING_Hooks->call('Cart', 'Delete');

      CLICSHOPPING::redirect(null, 'Cart');
    }
  }
