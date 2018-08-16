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

  namespace ClicShopping\Sites\Common;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class B2BCommon {
/**
 * Display the payment mode in different mode B2B or not
 *
 * @param string $product_price_d, the price of the product or not
 * @access public
 */

    public static function getPaymentUnallowed($pay_check) {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (CLICSHOPPING::getSite() != 'ClicShoppingAdmin' ) {

        $customer_group_id = $CLICSHOPPING_Customer->getCustomersGroupID();

        if (($CLICSHOPPING_Customer->isLoggedOn()) && ($customer_group_id != 0)) {

          $QpaymentsNotAllowed = $CLICSHOPPING_Db->prepare('select group_payment_unallowed
                                                    from :table_customers_groups
                                                    where customers_group_id = :customers_group_id
                                                  ');
          $QpaymentsNotAllowed->bindInt(':customers_group_id', (int)$customer_group_id);
          $QpaymentsNotAllowed->execute();

          $payments_not_allowed = $QpaymentsNotAllowed->fetch();

          $payments_unallowed = explode(",",$payments_not_allowed['group_payment_unallowed']);
          $clearance = (in_array($pay_check, $payments_unallowed)) ? true : false;

        } else if ($CLICSHOPPING_Customer->isLoggedOn()) {
          $clearance = true;
        } else {
          $clearance = false;
        }

      } else {

        $Qpayments = $CLICSHOPPING_Db->prepare('select group_payment_unallowed
                                          from :table_customers_groups
                                       ');

        $Qpayments->execute();

        $payments_not_allowed = $Qpayments->fetch();
        $payments_unallowed = explode (",",$payments_not_allowed['group_payment_unallowed']);
        $clearance = (!in_array ($pay_check, $payments_unallowed)) ?  true : false;
      }

      return $clearance;
    }

/**
 * Not Display  the payment module if customer_group = 0
 * @param string $customer_group_id, the group of the customer
 * @access public
 **/
    public static function getPaymentNotDisplayPayment() {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $customer_group_id = $CLICSHOPPING_Customer->getID();

      return $customer_group_id;
    }

/**
 * Display the shipping mode in different mode B2B or not
 *
 * @param string $product_price_d, the price of the product or not
 * @access public
 */
    public static function getShippingUnallowed($shipping_check) {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (CLICSHOPPING::getSite() != 'ClicShoppingAdmin' ) {

        $customer_group_id = $CLICSHOPPING_Customer->getCustomersGroupID();

        if (($CLICSHOPPING_Customer->isLoggedOn()) && ($customer_group_id != 0)) {

          $QshippingNotAllowed = $CLICSHOPPING_Db->prepare('select group_shipping_unallowed
                                                    from :table_customers_groups
                                                    where customers_group_id = :customers_group_id
                                                  ');
          $QshippingNotAllowed->bindInt(':customers_group_id', $customer_group_id);
          $QshippingNotAllowed->execute();

          $shipping_not_allowed = $QshippingNotAllowed->fetch();

          $shipping_unallowed = explode(",",$shipping_not_allowed['group_shipping_unallowed']);
          $shipping_clearance = (in_array($shipping_check, $shipping_unallowed)) ? true : false;
        } elseif ($CLICSHOPPING_Customer->isLoggedOn()) {
          $shipping_clearance = true;
        } else {
          $shipping_clearance = false;
        }
      } else {
        $Qshipping = $CLICSHOPPING_Db->prepare('select group_shipping_unallowed
                                                from :table_customers_groups
                                               ');

        $Qshipping->execute();

        $shipping_not_allowed = $Qshipping->fetch();
        $shipping_unallowed = explode (",",$shipping_not_allowed['group_payment_unallowed']);
        $shipping_clearance = (!in_array ($shipping_check, $shipping_unallowed)) ?  true : false;
      }

      return $shipping_clearance;
    }


/**
 * Display the taxe or not mode in different mode B2B or not
 *
 * @param string $product_price_d, the price of the product or not
 * @access public
 * $tax_check no variable
 */
    public static function getTaxUnallowed() {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (CLICSHOPPING::getSite() != 'ClicShoppingAdmin' ) {

        $customer_group_id = $CLICSHOPPING_Customer->getCustomersGroupID();

        if (($CLICSHOPPING_Customer->isLoggedOn()) && ($customer_group_id != 0)) {

          $Qtaxb2b = $CLICSHOPPING_Db->prepare('select group_order_taxe
                                                from :table_customers_groups
                                                where customers_group_id = :customers_group_id
                                              ');
          $Qtaxb2b->bindInt(':customers_group_id', $customer_group_id);
          $Qtaxb2b->execute();

          if ($Qtaxb2b->valueInt('group_order_taxe') == 1) {
            $tax_clearance = false;
          } else {
            $tax_clearance = true;
          }
        } else {
          $tax_clearance = true;
        }
      } else {
        $tax_clearance = true;
      }

      return $tax_clearance;
    }
  }