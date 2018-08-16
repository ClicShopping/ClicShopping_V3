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

  namespace ClicShopping\Sites\Shop\Pages\Checkout\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class ShippingAddress extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      global $process, $error;

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
      $CLICSHOPPING_NavigationHistory = Registry::get('NavigationHistory');
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');
      $CLICSHOPPING_Order = Registry::get('Order');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

// if the customer is not logged on, redirect them to the login page
      if (!$CLICSHOPPING_Customer->isLoggedOn()) {
        $CLICSHOPPING_NavigationHistory->setSnapshot();
        CLICSHOPPING::redirect('index.php', 'Account&LogIn');
      }

// if there is nothing in the customers cart, redirect them to the shopping cart page
      if ($CLICSHOPPING_ShoppingCart->count_contents() < 1) {
        CLICSHOPPING::redirect('index.php', 'Cart');
      }

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
      if ($CLICSHOPPING_Order->content_type == 'virtual') {
        $_SESSION['shipping'] = false;
        $_SESSION['sendto'] = false;
        CLICSHOPPING::redirect('index.php', 'Checkout&Billing');
      }

      $error = false;
      $process = false;

      if (isset($_GET['newcustomer'])) {
        $QaddresseDefault = $CLICSHOPPING_Db->prepare('select customers_default_address_id
                                                 from :table_customers
                                                 where customers_id = :customers_id
                                               ');
        $QaddresseDefault->bindInt(':customers_id',(int)$CLICSHOPPING_Customer->getID());
        $QaddresseDefault->execute();

        if ($QaddresseDefault->rowCount() == 1) {
          CLICSHOPPING::redirect('index.php','Account&AddressBookProcess&newcustomer=1&shopping=1&edit=' . $QaddresseDefault->valueInt('customers_default_address_id'));
        }
      }

// if no shipping destination address was selected, use their own address as default
      if (!isset($_SESSION['sendto'])) {
        $_SESSION['sendto'] = $CLICSHOPPING_Customer->getDefaultAddressID();
      }

// templates
      $this->page->setFile('shipping_address.php');
//Content
      $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('checkout_shipping_address');
//language
      $CLICSHOPPING_Language->loadDefinitions('checkout_shipping_address');

      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_1'), CLICSHOPPING::link('index.php','Checkout&Shipping'));
      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_2'), CLICSHOPPING::link('index.php','Checkout&ShippingAddress'));
    }
  }
