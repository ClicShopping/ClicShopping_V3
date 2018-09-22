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


  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  class CustomerShop {
    protected $_is_logged_on = false;
    protected $_data = [];
    protected $db;

    public function __construct() {
      if ( isset($_SESSION['customer']) ) {
        $this->_data =& $_SESSION['customer'];
      }

      if ( isset($_SESSION['customer_group_id']) ) {
        $this->_data1 =& $_SESSION['customer_group_id'];
      }

      if ( isset($this->_data['id']) && is_numeric($this->_data['id']) && ($this->_data['id'] > 0) ) {
        $this->setIsLoggedOn(true);
      }

      $this->db = Registry::get('Db');
    }

    public function setIsLoggedOn($state) {
      if ( !is_bool($state) ) {
        $state = false;
      }

      $this->_is_logged_on = $state;
    }

    public function isLoggedOn() {
      return $this->_is_logged_on;
    }

    public function get($key = null) {
      if ( isset($key) ) {
        return $this->_data[$key];
      }

      return $this->_data;
    }

    public function getID() {
      return $this->get('id');
    }

    public function getFirstName() {
      return $this->get('first_name');
    }

    public function getLastName() {
      return $this->get('last_name');
    }

    public function getName() {
      $name = '';

      if ( isset($this->_data['first_name']) ) {
        $name .= $this->_data['first_name'];
      }

      if ( isset($this->_data['last_name']) ) {
        if ( !empty($name) ) {
          $name .= ' ';
        }

        $name .= $this->_data['last_name'];
      }

      return $name;
    }

    public function getGender() {
      return $this->get('gender');
    }

    public function hasEmailAddress() {
      return isset($this->_data['email_address']);
    }

    public function getEmailAddress() {
      return $this->_data['email_address'];
    }

    public function getTelephone() {
      return $this->get('telephone');
    }

    public function getCountryID() {
      return $this->_data['country_id'];
    }

    public function getZoneID() {
      return $this->_data['zone_id'];
    }

    public function getDefaultAddressID() {
      return $this->_data['default_address_id'];
    }

// B2B
    public function getCustomersGroupID() {
      $customersgroupid = 0;
      if (isset($this->_data1['customers_group_id'])) {
        $customersgroupid = $this->_data1['customers_group_id'];
      }
      return $customersgroupid;
    }


    public function setData($id) {
      $this->_data = [];

      if ( is_numeric($id) && ($id > 0) ) {
        $Qcustomer = $this->db->prepare('select customers_gender,
                                                 customers_firstname,
                                                 customers_lastname,
                                                 customers_email_address,
                                                 customers_telephone,
                                                 customers_default_address_id
                                          from :table_customers
                                          where customers_id = :customers_id
                                        ');
        $Qcustomer->bindInt(':customers_id', $id);
        $Qcustomer->execute();

// B2B
        $QcustomerGroup = $this->db->prepare('select customers_group_id
                                               from :table_customers
                                               where customers_id = :customers_id
                                              ');
        $QcustomerGroup->bindInt(':customers_id',  $id );
        $Qcustomer->bindInt(':customers_id', $id);
        $QcustomerGroup->execute();

        if ( $QcustomerGroup->fetch() !== false ) {
          $this->setCustomersGroupID($QcustomerGroup->value('customers_group_id'));
          $_SESSION['customer_group_id'] = $this->_data1;
        }

        if ( $Qcustomer->fetch() !== false ) {
          $this->setIsLoggedOn(true);
          $this->setID($id);
          $this->setGender($Qcustomer->value('customers_gender'));
          $this->setFirstName($Qcustomer->value('customers_firstname'));
          $this->setLastName($Qcustomer->value('customers_lastname'));
          $this->setEmailAddress($Qcustomer->value('customers_email_address'));
          $this->setTelephone($Qcustomer->value('customers_telephone'));

          if ( $Qcustomer->valueInt('customers_default_address_id') > 0 ) {
            $Qab = $this->db->prepare('select entry_country_id,
                                               entry_zone_id
                                        from :table_address_book
                                        where address_book_id = :address_book_id
                                        and customers_id = :customers_id
                                      ');
            $Qab->bindInt(':address_book_id', $Qcustomer->valueInt('customers_default_address_id'));
            $Qab->bindInt(':customers_id', $id);
            $Qab->execute();

            if ( $Qab->fetch() !== false ) {
              $this->setCountryID($Qab->valueInt('entry_country_id'));
              $this->setZoneID($Qab->valueInt('entry_zone_id'));
              $this->setDefaultAddressID($Qcustomer->valueInt('customers_default_address_id'));
            }
          }

          $_SESSION['customer'] = $this->_data;

        }
      }

      return !empty($this->_data);
    }

    public function setID($id) {
      if ( is_numeric($id) && ($id > 0) ) {
        $this->_data['id'] = $id;
      }
    }

    public function setDefaultAddressID($id) {
      if ( is_numeric($id) && ($id > 0) ) {

        if ( !isset($this->_data['default_address_id']) || ($this->_data['default_address_id'] != $id) ) {

          $Qupdate = $this->db->prepare('update :table_customers
                                          set customers_default_address_id = :customers_default_address_id
                                          where customers_id = :customers_id'
                                        );
          $Qupdate->bindInt(':customers_default_address_id', $id);
          $Qupdate->bindInt(':customers_id', $this->getID());
          $Qupdate->execute();
        }

        $this->_data['default_address_id'] = $id;
      }
    }

    public function hasDefaultAddress() {
      return isset($this->_data['default_address_id']) && is_numeric($this->_data['default_address_id']);
    }

    public function setGender($gender) {
      if ( (strtolower($gender) == 'm') || (strtolower($gender) == 'f') ) {
        $this->_data['gender'] = strtolower($gender);
      }
    }

    public function setFirstName($first_name) {
      $this->_data['first_name'] = $first_name;
    }

    public function setLastName($last_name) {
      $this->_data['last_name'] = $last_name;
    }

    public function setEmailAddress($email_address) {
      $this->_data['email_address'] = $email_address;
    }

    public function setTelephone($telephone) {
      $this->_data['telephone'] = $telephone;
    }

    public function setCountryID($id) {
      $this->_data['country_id'] = $id;
    }

    public function setZoneID($id) {
      $this->_data['zone_id'] = $id;
    }

// B2B
    public function setCustomersgroupID($id) {
      $this->_data1['customers_group_id'] = $id;
    }


    public function reset() {
      $this->_is_logged_on = false;
      $this->_data = [];

      if ( isset($_SESSION['customer']) ) {
        unset($_SESSION['customer']);
      }

// B2B
      if ( isset($_SESSION['customer_group_id']) ) {
        unset($_SESSION['customer_group_id']);
      }
    }


/**
 * Customers Greeting
 * @return string $greeting_string
 * @access public
 */

    public function customerGreeting() {
      if ($this->isLoggedOn()) {
        $greeting_string = CLICSHOPPING::getDef('text_greeting_personal',['first_name' => HTML::outputProtected($this->getFirstName()),
                                                                            'url_products_new' => CLICSHOPPING::link('index.php','Products&ProductsNew'),
                                                                            'url_logoff' => CLICSHOPPING::link('index.php','Account&LogOff')
                                                                          ]
                                                );
      } else {
        if (MODE_MANAGEMENT_B2C_B2B == 'B2C_B2B' || MODE_MANAGEMENT_B2C_B2B =='B2B') {
          $greeting_string = CLICSHOPPING::getDef('text_greeting_guest',['url_login' => CLICSHOPPING::redirect('index.php', 'Account&LogIn'),
                                                                          'url_create_account' => CLICSHOPPING::link('index.php', 'Account&Create'),
                                                                          'url_create_account_pro' => CLICSHOPPING::link('Account.php', 'Account&CreatePro')
                                                                         ]
                                                  );
        } else {
          $greeting_string = CLICSHOPPING::getDef('text_greeting_guest',['url_login' => CLICSHOPPING::redirect('index.php', 'Account&LogIn'),
                                                                          'url_products_new' => CLICSHOPPING::link('index.php','Products&ProductsNew')
                                                                          ]
                                                 );
        }
      }

      return $greeting_string;
    }
  }