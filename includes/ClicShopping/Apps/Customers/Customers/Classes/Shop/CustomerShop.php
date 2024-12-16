<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Customers\Customers\Classes\Shop;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;
use function is_null;

/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */
class CustomerShop
{
  protected bool $_is_logged_on = false;
  protected array $_data = [];
  protected array $_data1 = [];

  private mixed $db;

  public function __construct()
  {
    if (isset($_SESSION['customer'])) {
      $this->_data =& $_SESSION['customer'];
    }

    if (isset($_SESSION['customer_group_id'])) {
      $this->_data1 =& $_SESSION['customer_group_id'];
    }

    if (isset($this->_data['id']) && is_numeric($this->_data['id']) && ($this->_data['id'] > 0)) {
      $this->setIsLoggedOn(true);
    }

    $this->db = Registry::get('Db');
  }

  /**
   * Sets the logged-on state for the user.
   *
   * @param bool $state Represents the logged-on state. True if the user is logged on, false otherwise.
   * @return void
   */
  public function setIsLoggedOn(bool $state)
  {
    if ($state === true) {
      $this->_is_logged_on = true;
    } else {
      $this->_is_logged_on = false;
    }
  }

  /**
   * @return bool
   */
  public function isLoggedOn(): bool
  {
    if ($this->_is_logged_on === true) {
      return true;
    }

    return false;
  }

  /**
   * Retrieves a value from the internal data storage.
   *
   * @param string|null $key The key to retrieve the value for. If null, the entire data storage is returned.
   * @return mixed The value associated with the specified key, or the entire data storage if no key is provided.
   */
  public function get(string $key = null)
  {
    if (isset($key)) {
      return $this->_data[$key];
    } else {
      return $this->_data;
    }
  }

  /**
   * @return int
   */
  public function getID(): int
  {
    if (isset($this->_data['id']) && is_numeric($this->_data['id'])) {
      return (int)$this->_data['id'];
    }

    return 0;
  }

  /**
   * Retrieves the first name of the user if available.
   *
   * @return string|bool Returns the first name as a string if it exists, or false if not set.
   */
  public function getFirstName(): string|bool
  {
    if (isset($this->_data['first_name'])) {
      return $this->_data['first_name'];
    }

    return false;
  }

  /**
   * @return string|bool
   */
  public function getLastName(): string|bool
  {
    if (isset($this->_data['last_name'])) {
      return $this->_data['last_name'];
    }

    return false;
  }

  /**
   * @return string
   */
  public function getName(): string
  {
    $name = '';

    if (isset($this->_data['first_name'])) {
      $name .= $this->_data['first_name'];
    }

    if (isset($this->_data['last_name'])) {
      if (!empty($name)) {
        $name .= ' ';
      }

      $name .= $this->_data['last_name'];
    }

    return $name;
  }

  /**
   * Retrieves the gender of the customer.
   *
   * @return string|bool Returns the gender as a string if set, otherwise returns false.
   */
  public function getGender(): string|bool
  {
    if (isset($this->_data['gender'])) {
      return $this->_data['gender'];
    }

    return false;
  }

  /**
   * @return bool
   */
  public function hasEmailAddress(): bool
  {
    return isset($this->_data['email_address']);
  }

  /**
   * @return string|bool
   */
  public function getEmailAddress(): string|bool
  {
    if (isset($this->_data['email_address'])) {
      return $this->_data['email_address'];
    }

    return false;
  }

  /**
   * Sets the telephone number for the customer.
   *
   * @param string|null $telephone The telephone number to set. Can be null to clear the telephone.
   * @return void
   */
  public function setTelephone(?string $telephone): void
  {
    $this->_data['customers_telephone'] = $telephone;
  }

  /**
   * Retrieves the customer's telephone number if available.
   *
   * @return string|bool The telephone number as a string if set, or false if not available.
   */
  public function getTelephone(): string|bool
  {
    if (isset($this->_data['customers_telephone'])) {
      return $this->_data['customers_telephone'];
    }

    return false;
  }

  /**
   * Sets the customer's cellular phone number.
   *
   * @param string|null $telephone The cellular phone number to set. Can be null.
   * @return void
   */
  public function setCellularPhone(?string $telephone): void
  {
    $this->_data['customers_cellular_phone'] = $telephone;
  }

  /**
   * Retrieves the customer's cellular phone number if available.
   *
   * @return string|bool Returns the cellular phone number as a string if it exists, or false if not set.
   */
  public function getCellularPhone(): string|bool
  {
    if (isset($this->_data['customers_cellular_phone'])) {
      return $this->_data['customers_cellular_phone'];
    }

    return false;
  }

  /**
   * Retrieves the country ID associated with the current instance.
   *
   * @return int|null The country ID if available, otherwise null.
   */
  public function getCountryID():  int|null
  {
    static $country_id = null;

    if (is_null($country_id)) {
      if (isset($this->_data['country_id'])) {
        $country_id = $this->_data['country_id'];
      }
    }

    return $country_id;
  }

  /**
   * @return int|null
   */
  public function getZoneID():  int|null
  {
    static $zone_id = null;

    if (is_null($zone_id)) {
      if (isset($this->_data['zone_id'])) {
        $zone_id = $this->_data['zone_id'];
      }
    }

    return $zone_id;
  }

  /**
   * @return int|null
   */
  public function getDefaultAddressID():  int|null
  {
    static $id = null;

    if (is_null($id)) {
      if (isset($this->_data['default_address_id'])) {
        $id = $this->_data['default_address_id'];
      }
    }

    return $id;
  }

  /**
   * @return int
   */
  public function getCustomersGroupID(): int
  {
    $customersGroupId = 0;

    if (isset($this->_data1['customers_group_id'])) {
      $customersGroupId = $this->_data1['customers_group_id'];
    }

    return $customersGroupId;
  }

  /**
   * Sets customer data based on the provided customer ID and initializes session variables.
   *
   * @param int $id The ID of the customer to fetch data for.
   * @return bool True if the customer data was successfully set, false otherwise.
   */
  public function setData(int $id): bool
  {
    $this->_data = [];

    if (is_numeric($id) && ($id > 0)) {
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
      $QcustomerGroup->bindInt(':customers_id', $id);
      $Qcustomer->bindInt(':customers_id', $id);
      $QcustomerGroup->execute();

      if ($QcustomerGroup->fetch() !== false) {
        $this->setCustomersGroupID($QcustomerGroup->value('customers_group_id'));
        $_SESSION['customer_group_id'] = $this->_data1;
      }

      if ($Qcustomer->fetch() !== false) {
        $this->setIsLoggedOn(true);
        $this->setID($id);
        $this->setGender($Qcustomer->value('customers_gender'));
        $this->setFirstName($Qcustomer->value('customers_firstname'));
        $this->setLastName($Qcustomer->value('customers_lastname'));
        $this->setEmailAddress($Qcustomer->value('customers_email_address'));
        $this->setTelephone($Qcustomer->value('customers_telephone'));

        if ($Qcustomer->valueInt('customers_default_address_id') > 0) {
          $Qab = $this->db->prepare('select entry_country_id,
                                               entry_zone_id
                                        from :table_address_book
                                        where address_book_id = :address_book_id
                                        and customers_id = :customers_id
                                      ');
          $Qab->bindInt(':address_book_id', $Qcustomer->valueInt('customers_default_address_id'));
          $Qab->bindInt(':customers_id', $id);
          $Qab->execute();

          if ($Qab->fetch() !== false) {
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

  /**
   * Set the ID for the object.
   *
   * @param int $id The ID to be set. Must be a positive numeric value.
   * @return void
   */
  public function setID(int $id): void
  {
    if (is_numeric($id) && ($id > 0)) {
      $this->_data['id'] = $id;
    } else {
      $this->_data['id'] = false;
    }
  }

  /**
   * Sets the default address ID for the customer.
   *
   * @param int $id The ID of the address to be set as default. Must be a positive integer.
   * @return void
   */
  public function setDefaultAddressID(int $id): void
  {
    if (is_numeric($id) && ($id > 0)) {
      if (!isset($this->_data['default_address_id']) || ($this->_data['default_address_id'] != $id)) {
        $update_sql = [
          'customers_default_address_id' => $id
        ];

        $this->db->save('customers', $update_sql, ['customers_id' => $this->getID()]);
      }

      $this->_data['default_address_id'] = $id;
    } else {
      $this->_data['default_address_id'] = false;
    }
  }

  /**
   * @return bool
   */
  public function hasDefaultAddress(): bool
  {
    if (isset($this->_data['default_address_id']) && is_numeric($this->_data['default_address_id'])) {
      return true;
    }

    return false;
  }

  /**
   * Sets the gender value after validating it as 'm' or 'f'.
   *
   * @param string|null $gender The gender value to set. Should be 'm' or 'f' (case insensitive).
   * @return void
   */
  public function setGender(?string $gender): void
  {
    if ((mb_strtolower($gender) == 'm') || (mb_strtolower($gender) == 'f')) {
      $this->_data['gender'] = mb_strtolower($gender);
    } else {
      $this->_data['gender'] = false;
    }
  }

  /**
   * Sets the first name of the customer.
   *
   * @param string|null $first_name The first name to set. Can be null.
   * @return void
   */
  public function setFirstName(?string $first_name): void
  {
    $this->_data['first_name'] = $first_name;
  }

  /**
   * Sets the last name for the current instance.
   *
   * @param string|null $last_name The last name to set. Nullable.
   * @return void
   */
  public function setLastName(?string $last_name): void
  {
    $this->_data['last_name'] = $last_name;
  }

  /**
   * Sets the email address for the current instance.
   *
   * @param string $email_address The email address to be set.
   * @return void
   */
  public function setEmailAddress(string $email_address): void
  {
    $this->_data['email_address'] = $email_address;
  }

  /**
   * Retrieves the guest account status for a given customer ID.
   *
   * @param int $id The unique identifier of the customer.
   * @return int|null The guest account status of the customer, or null if not available.
   */
  public function getCustomerGuestAccount(int $id):  int|null
  {
    $Qresult = $this->db->get('customers', 'customer_guest_account', ['customers_id' => (int)$id]);

    return $Qresult->valueInt('customer_guest_account');
  }

  /**
   * Retrieves the customer's IP address based on their customer ID.
   *
   * @return string The IP address of the customer.
   */
  public function getCustomerIp(): string
  {
    $Qresult = $this->db->get('customers', 'client_computer_ip', ['customers_id' => $this->getID()], null, 1);

    return $Qresult->value('client_computer_ip');
  }

  /**
   * Sets the country ID for the instance.
   *
   * @param int $id The ID of the country to set.
   * @return void
   */
  public function setCountryID(int $id): void
  {
    $this->_data['country_id'] = $id;
  }

  /**
   * Sets the zone ID for the current data.
   *
   * @param int $id The ID of the zone to be set.
   * @return void
   */
  public function setZoneID(int $id): void
  {
    $this->_data['zone_id'] = $id;
  }

  /**
   * Sets the customer's group ID.
   *
   * @param int $id The ID of the customer group to set.
   * @return void
   */
  public function setCustomersgroupID(int $id): void
  {
    $this->_data1['customers_group_id'] = $id;
  }

  /**
   * Resets the customer session and user data.
   *
   * This method logs out the customer by unsetting session variables
   * related to the customer and clearing internal customer-related data.
   *
   * @return void
   */
  public function reset(): void
  {
    $this->_is_logged_on = false;
    $this->_data = [];

    if (isset($_SESSION['customer'])) {
      unset($_SESSION['customer']);
    }

// B2B
    if (isset($_SESSION['customer_group_id'])) {
      unset($_SESSION['customer_group_id']);
    }
  }


  /**
   * Generates a greeting message for the customer based on their login status.
   *
   * @return string The personalized or guest greeting message for the customer.
   */
  public function customerGreeting(): string
  {
    if ($this->isLoggedOn()) {
      $text_array = [
        'first_name' => HTML::outputProtected($this->getFirstName()),
        'url_products_new' => CLICSHOPPING::link(null, 'Products&ProductsNew'),
        'url_logoff' => CLICSHOPPING::link(null, 'Account&LogOff')
      ];

      $greeting_string = CLICSHOPPING::getDef('text_greeting_personal', $text_array);
    } else {
      if (MODE_MANAGEMENT_B2C_B2B == 'B2C_B2B' || MODE_MANAGEMENT_B2C_B2B == 'B2B') {
        $text_array = [
          'url_login' => CLICSHOPPING::redirect(null, 'Account&LogIn'),
          'url_create_account' => CLICSHOPPING::link(null, 'Account&Create'),
          'url_create_account_pro' => CLICSHOPPING::link('Account.php', 'Account&CreatePro')
        ];

        $greeting_string = CLICSHOPPING::getDef('text_greeting_guest', $text_array);
      } else {
        $text_array = [
          'url_login' => CLICSHOPPING::redirect(null, 'Account&LogIn'),
          'url_products_new' => CLICSHOPPING::link(null, 'Products&ProductsNew')
        ];

        $greeting_string = CLICSHOPPING::getDef('text_greeting_guest', $text_array);
      }
    }

    return $greeting_string;
  }

  /**
   * Checks whether the customer has any product notifications set.
   *
   * @return bool Returns true if the customer has product notifications, otherwise false.
   */
  public function hasProductNotifications(): bool
  {
    $Qcheck = $this->db->prepare('select products_id
                                    from :table_products_notifications
                                    where customers_id = :customers_id
                                    limit 1
                                    ');
    $Qcheck->bindInt(':customers_id', $this->_data['id']);
    $Qcheck->execute();

    return ($Qcheck->fetch() !== false);
  }

  /**
   * Retrieves the product notifications for the current customer.
   *
   * @return mixed Returns the product notification data for the customer, which includes product IDs and names.
   */
  public function getProductNotifications(): mixed
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qproducts = $this->db->prepare('select pd.products_id,
                                             pd.products_name
                                      from :table_products_description pd,
                                           :table_products_notifications pn
                                      where pn.customers_id = :customers_id
                                        and pn.products_id = pd.products_id
                                        and pd.language_id = :language_id
                                      order by pd.products_name
                                      ');
    $Qproducts->bindInt(':customers_id', $this->_data['id']);
    $Qproducts->bindInt(':language_id', $CLICSHOPPING_Language->getID());
    $Qproducts->execute();

    return $Qproducts;
  }
}