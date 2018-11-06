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

  namespace ClicShopping\Sites\Shop\Pages\Account\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;


  class LogOff extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');

      $CLICSHOPPING_Customer->reset();

      if ( isset($_SESSION['sendto']) ) {
        unset($_SESSION['sendto']);
      }

      if ( isset($_SESSION['billto']) ) {
        unset($_SESSION['billto']);
      }

      if ( isset($_SESSION['shipping']) ) {
        unset($_SESSION['shipping']);
      }

      if ( isset($_SESSION['payment']) ) {
        unset($_SESSION['payment']);
      }

      if ( isset($_SESSION['comments']) ) {
        unset($_SESSION['comments']);
      }

      if ( isset($_SESSION['free_shipping']) ) {
        unset($_SESSION['free_shipping']);
      }

      if ( isset($_SESSION['login_customer_id']) ) {
        unset($_SESSION['login_customer_id']);
      }

      $CLICSHOPPING_ShoppingCart->reset();

      Registry::get('Hooks')->call('Account', 'Logout');

      CLICSHOPPING::redirect();
    }
  }

