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

  namespace ClicShopping\Sites\Shop\Pages\Products\Actions\Notify;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class Add extends \ClicShopping\OM\PagesActionsAbstract  {

    public function execute()  {

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_NavigationHistory = Registry::get('NavigationHistory');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if ($CLICSHOPPING_Customer->isLoggedOn()) {
        if (isset($_GET['products_id'])) {
          $notify = (int)$CLICSHOPPING_ProductsCommon->getID();
        } elseif (isset($_GET['notify'])) {
          $notify = $_GET['notify'];
        } elseif (isset($_POST['notify'])) {
          $notify = $_POST['notify'];
        } else {
          CLICSHOPPING::redirect(null, 'Products&Description&products_id=' . (int)$CLICSHOPPING_ProductsCommon->getID());
        }

        if (!is_array($notify)) $notify = array($notify);

        for ($i=0, $n=count($notify); $i<$n; $i++) {

          $Qcheck = $CLICSHOPPING_Db->get('products_notifications', 'products_id', ['customers_id' => (int)$CLICSHOPPING_Customer->getID(),
                                                                              'products_id' => (int)$notify[$i]
                                                                             ]
                                    );

          if ($Qcheck->fetch() === false) {
            $CLICSHOPPING_Db->save('products_notifications', ['products_id' => (int)$notify[$i],
                                                        'customers_id' => (int)$CLICSHOPPING_Customer->getID(),
                                                        'date_added' => 'now()'
                                                       ]
                            );
          }
        }

        $CLICSHOPPING_Hooks->call('Products', 'Add');

        $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('success_notifications_updated'), 'success', 'products');

        CLICSHOPPING::redirect(null, 'Products&Description&products_id=' . (int)$CLICSHOPPING_ProductsCommon->getID());

      } else {
        $CLICSHOPPING_NavigationHistory->setSnapshot();
        CLICSHOPPING::redirect(null, 'Account&LogIn');
      }
    }
  }
