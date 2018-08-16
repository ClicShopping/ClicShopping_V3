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

  namespace ClicShopping\Apps\Payment\PayPal\Classes;


  class PaypalLogin {

    public static function mlPaypalLoginGetAttributes() {
      return array('personal' => array('full_name' => 'profile',
                                       'date_of_birth' => 'profile',
                                       'age_range' => 'https://uri.paypal.com/services/paypalattributes',
                                       'gender' => 'profile'),
                   'address' => array('email_address' => 'email',
                                      'street_address' => 'address',
                                      'city' => 'address',
                                      'state' => 'address',
                                      'country' => 'address',
                                      'zip_code' => 'address',
                                      'phone' => 'phone'),
                   'account' => array('account_status' => 'https://uri.paypal.com/services/paypalattributes',
                                      'account_type' => 'https://uri.paypal.com/services/paypalattributes',
                                      'account_creation_date' => 'https://uri.paypal.com/services/paypalattributes',
                                      'time_zone' => 'profile',
                                      'locale' => 'profile',
                                      'language' => 'profile'),
                   'checkout' => array('seamless_checkout' => 'https://uri.paypal.com/services/expresscheckout'));
    }


    public function mlPaypalLoginGetRequiredAttributes() {
      return array('full_name',
                   'email_address',
                   'street_address',
                   'city',
                   'state',
                   'country',
                   'zip_code');
    }


  }