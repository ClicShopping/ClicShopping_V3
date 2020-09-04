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

  namespace ClicShopping\Sites\Shop\Pages\Products\Actions\Notify;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class Delete extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_NavigationHistory = Registry::get('NavigationHistory');
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if ($CLICSHOPPING_Customer->isLoggedOn() && $CLICSHOPPING_ProductsCommon->getID()) {
        $Qcheck = $CLICSHOPPING_Db->get('products_notifications', 'products_id', [
          'customers_id' => (int)$CLICSHOPPING_Customer->getID(),
          'products_id' => $CLICSHOPPING_ProductsCommon->getID()
          ]
        );

        if ($Qcheck->fetch() !== false) {
          $CLICSHOPPING_Db->delete('products_notifications', [
            'customers_id' => (int)$CLICSHOPPING_Customer->getID(),
            'products_id' => $CLICSHOPPING_ProductsCommon->getID()
            ]
          );
        }

        $CLICSHOPPING_Hooks->call('Products', 'Delete');

        CLICSHOPPING::redirect(null, 'Products&Description&products_id=' . $CLICSHOPPING_ProductsCommon->getID());

      } else {
        $CLICSHOPPING_NavigationHistory->setSnapshot();
        CLICSHOPPING::redirect(null, 'Account&LogIn');
      }
    }
  }