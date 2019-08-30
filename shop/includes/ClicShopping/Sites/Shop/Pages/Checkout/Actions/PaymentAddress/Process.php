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

  namespace ClicShopping\Sites\Shop\Pages\Checkout\Actions\PaymentAddress;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Process extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $error = false;

      if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
// process a new billing address
        if (!$CLICSHOPPING_Customer->hasDefaultAddress() || (isset($_POST['firstname']) && !empty($_POST['firstname']) && isset($_POST['lastname']) && !empty($_POST['lastname']) && isset($_POST['street_address']) && !empty($_POST['street_address']))) {

          if (ACCOUNT_GENDER == 'true') {
            $gender = HTML::sanitize($_POST['gender']);
          } else {
            $gender = '';
          }

          if (ACCOUNT_COMPANY == 'true') {
            $company = HTML::sanitize($_POST['company']);
          } else {
            $company = '';
          }

          $firstname = HTML::sanitize($_POST['firstname']);
          $lastname = HTML::sanitize($_POST['lastname']);
          $street_address = HTML::sanitize($_POST['street_address']);

          if (ACCOUNT_SUBURB == 'true') {
            $suburb = HTML::sanitize($_POST['suburb']);
          } else {
            $suburb = '';
          }

          $postcode = HTML::sanitize($_POST['postcode']);
          $city = HTML::sanitize($_POST['city']);
          $country = HTML::sanitize($_POST['country']);

          if (isset($_POST['telephone'])) {
            $entry_telephone = HTML::sanitize($_POST['telephone']);
          } else {
            $entry_telephone = '';
          }


          if (ACCOUNT_STATE == 'true') {
            if (isset($_POST['zone_id'])) {
              $zone_id = HTML::sanitize($_POST['zone_id']);
            } else {
              $zone_id = false;
            }
            $state = HTML::sanitize($_POST['state']);
          }

          if ((ACCOUNT_GENDER == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) {
            if (($gender != 'm') && ($gender != 'f')) {
              $error = true;

              $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_gender_error'), 'danger', 'checkout_address');
            }
          } else if ((ACCOUNT_GENDER_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0)) {
            if (($gender != 'm') && ($gender != 'f')) {
              $error = true;
              $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_gender_error_pro'), 'danger', 'checkout_address');
            }
          }

          if ((strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_first_name_error', ['min_length' => ENTRY_FIRST_NAME_MIN_LENGTH]), 'danger', 'checkout_address');
          } else if ((strlen($firstname) < ENTRY_FIRST_NAME_PRO_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0)) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_first_name_error_pro', ['min_length' => ENTRY_FIRST_NAME_PRO_MIN_LENGTH]), 'danger', 'checkout_address');
          }

          if ((strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_last_name_error', ['min_length' => ENTRY_LAST_NAME_MIN_LENGTH]), 'danger', 'checkout_address');

          } else if ((strlen($lastname) < ENTRY_LAST_NAME_PRO_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0)) {
            $error = true;
            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_last_name_error_pro', ['min_length' => ENTRY_LAST_NAME_PRO_MIN_LENGTH]), 'danger', 'checkout_address');
          }

          if ((strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_street_address_error', ['min_length' => ENTRY_STREET_ADDRESS_MIN_LENGTH]), 'danger', 'checkout_address');
          } else if ((strlen($street_address) < ENTRY_STREET_ADDRESS_PRO_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0)) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_street_address_error_pro', ['min_length' => ENTRY_STREET_ADDRESS_PRO_MIN_LENGTH]), 'danger', 'checkout_address');
          }

          if ((strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_post_code_error', ['min_length' => ENTRY_POSTCODE_MIN_LENGTH]), 'danger', 'checkout_address');

          } else if ((strlen($postcode) < ENTRY_POSTCODE_PRO_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0)) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_post_code_error_pro', ['min_length' => ENTRY_POSTCODE_PRO_MIN_LENGTH]), 'danger', 'checkout_address');
          }

          if ((strlen($city) < ENTRY_CITY_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_city_error', ['min_length' => ENTRY_CITY_MIN_LENGTH]), 'danger', 'checkout_address');

          } else if ((strlen($city) < ENTRY_CITY_PRO_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0)) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_city_error_pro', ['min_length' => ENTRY_CITY_PRO_MIN_LENGTH]), 'danger', 'checkout_address');
          }

          if (((ACCOUNT_STATE == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_STATE_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
            $zone_id = 0;

            $Qcheck = $CLICSHOPPING_Db->prepare('select zone_id
                                                 from :table_zones
                                                 where zone_country_id = :zone_country_id
                                                 and zone_status = 0
                                                 limit 1
                                                 ');
            $Qcheck->bindInt(':zone_country_id', $country);
            $Qcheck->execute();

            $entry_state_has_zones = ($Qcheck->fetch() !== false);

            if ($entry_state_has_zones === true) {
              if (ACCOUNT_STATE_DROPDOWN == 'true') {
                $Qzone = $CLICSHOPPING_Db->prepare('select distinct zone_id
                                                     from :table_zones
                                                     where zone_country_id = :zone_country_id
                                                     and (zone_name = :zone_name or zone_code = :zone_code)
                                                     and zone_status = 0
                                                   ');

                $Qzone->bindInt(':zone_country_id', $country);
                $Qzone->bindValue(':zone_name', $state);
                $Qzone->bindValue(':zone_code', $state);
                $Qzone->execute();
              } else {
                $Qzone = $CLICSHOPPING_Db->prepare('select distinct zone_id
                                                     from :table_zones
                                                     where zone_country_id = :zone_country_id
                                                     and (zone_name = :zone_name or zone_code = :zone_code)
                                                     and zone_status = 0
                                                   ');

                $Qzone->bindInt(':zone_country_id', $country);
                $Qzone->bindValue(':zone_name', $state);
                $Qzone->bindValue(':zone_code', $state);
                $Qzone->execute();
              }

              if (count($Qzone->fetchAll()) === 1) {
                $zone_id = (int)$Qzone->result[0]['zone_id'];
              } else {
                $error = true;

                if ($CLICSHOPPING_Customer->getCustomersGroupID() == 0) {
                  $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_state_error_select'), 'danger', 'header');

                } else if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
                  $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_state_error_select_pro'), 'danger', 'header');
                }
              }
            } else {
              if (ACCOUNT_STATE_DROPDOWN == 'false') {
                if ((strlen($state) < ENTRY_STATE_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) {
                  $error = true;

                  $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_state_error', ['min_length' => ENTRY_STATE_MIN_LENGTH]), 'danger', 'header');
                } else if ((strlen($state) < ENTRY_STATE_PRO_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0)) {
                  $error = true;

                  $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_state_error_pro', ['min_length' => ENTRY_STATE_PRO_MIN_LENGTH]), 'danger', 'header');
                }
              }
            }
          } //end ACCOUNT_STATE == 'true'

// Clients B2C et B2B : Controle de la selection du pays
          if (!is_numeric($country) && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0 || $country < 1)) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_country_error'), 'error', 'header');
          } else if (!is_numeric($country) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0 || $country < 1)) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_country_error_pro'), 'error', 'header');
          }

          if ($error === false) {
            $sql_data_array = array('customers_id' => (int)$CLICSHOPPING_Customer->getID(),
              'entry_firstname' => $firstname,
              'entry_lastname' => $lastname,
              'entry_street_address' => $street_address,
              'entry_postcode' => $postcode,
              'entry_city' => $city,
              'entry_country_id' => (int)$country,
              'entry_telephone' => $entry_telephone
            );

            if (ACCOUNT_GENDER == 'true') $sql_data_array['entry_gender'] = $gender;
            if (ACCOUNT_COMPANY == 'true') $sql_data_array['entry_company'] = $company;
            if (ACCOUNT_SUBURB == 'true') $sql_data_array['entry_suburb'] = $suburb;
            if (ACCOUNT_STATE == 'true') {
              if ($zone_id > 0) {
                $sql_data_array['entry_zone_id'] = (int)$zone_id;
                $sql_data_array['entry_state'] = '';
              } else {
                $sql_data_array['entry_zone_id'] = '0';
                $sql_data_array['entry_state'] = $state;
              }
            }

            $CLICSHOPPING_Db->save('address_book', $sql_data_array);

            $_SESSION['billto'] = $CLICSHOPPING_Db->lastInsertId();

            if (!$CLICSHOPPING_Customer->hasDefaultAddress()) {
              $CLICSHOPPING_Customer->setCountryID($country);
              $CLICSHOPPING_Customer->setZoneID(($zone_id > 0) ? (int)$zone_id : '0');
              $CLICSHOPPING_Customer->setDefaultAddressID($_SESSION['billto']);
            }

            $CLICSHOPPING_Hooks->call('PaymentAddress', 'Process');

            if (isset($_SESSION['payment'])) {
              unset($_SESSION['payment']);
            }

            CLICSHOPPING::redirect(null, 'Checkout&Billing');
          }
// process the selected shipping destination
        } elseif (isset($_POST['address'])) {
          $reset_payment = false;

          if (isset($_SESSION['billto'])) {
            if ($_SESSION['billto'] != $_POST['address']) {
              if (isset($_SESSION['payment'])) {
                $reset_payment = true;
              }
            }
          }

          $_SESSION['billto'] = $_POST['address'];

          $Qcheck = $CLICSHOPPING_Db->prepare('select address_book_id
                                               from :table_address_book
                                               where address_book_id = :address_book_id
                                               and customers_id = :customers_id
                                              ');
          $Qcheck->bindInt(':address_book_id', (int)$_SESSION['billto']);
          $Qcheck->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
          $Qcheck->execute();

          if ($Qcheck->fetch() !== false) {

            $CLICSHOPPING_Hooks->call('PaymentAddress', 'Process');

            if ($reset_payment === true) {
              unset($_SESSION['payment']);
            }

            CLICSHOPPING::redirect(null, 'Checkout&Billing');
          } else {
            unset($_SESSION['billto']);
          }
        } else {

// no addresses to select from - customer decided to keep the current assigned address
          $_SESSION['billto'] = $CLICSHOPPING_Customer->getDefaultAddressID();

          CLICSHOPPING::redirect(null, 'Checkout&Billing');
        }
      }
    }
  }


