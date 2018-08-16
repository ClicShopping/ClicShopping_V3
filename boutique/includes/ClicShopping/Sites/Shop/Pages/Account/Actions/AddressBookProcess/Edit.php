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


  namespace ClicShopping\Sites\Shop\Pages\Account\Actions\AddressBookProcess;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\Sites\Shop\AddressBook;

  class Edit extends \ClicShopping\OM\PagesActionsAbstract  {

    public function execute()  {
      global $exists, $process, $entry_state_has_zones, $country;

      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if ( $exists === false ) {
        $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_nonexisting_address_book_entry'), 'error', 'addressbook');

        CLICSHOPPING::redirect('index.php','Account&AddressBook');
     }

      if (isset($_POST['action']) && $_POST['action'] == 'update' && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
        $process = true;
        $error = false;

        if (((ACCOUNT_GENDER == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_GENDER_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
          $gender = HTML::sanitize($_POST['gender']);
        }

        if (((ACCOUNT_COMPANY == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_COMPANY_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
          $company = HTML::sanitize($_POST['company']);
        }

        $firstname = HTML::sanitize($_POST['firstname']);
        $lastname = HTML::sanitize($_POST['lastname']);
        $telephone = HTML::sanitize($_POST['telephone']);
        $cellular_phone = HTML::sanitize($_POST['cellular_phone']);
        $fax = HTML::sanitize($_POST['fax']);

        $street_address = HTML::sanitize($_POST['street_address']);

        if (((ACCOUNT_SUBURB == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_SUBURB_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
          $suburb = HTML::sanitize($_POST['suburb']);
        }

        $postcode = HTML::sanitize($_POST['postcode']);
        $city = HTML::sanitize($_POST['city']);
        $country = HTML::sanitize($_POST['country']);

        if ((($CLICSHOPPING_Customer->getCustomersGroupID() == 0) && (ENTRY_TELEPHONE_MIN_LENGTH > 0)) || (($CLICSHOPPING_Customer->getCustomersGroupID() != 0) && (ENTRY_TELEPHONE_PRO_MIN_LENGTH > 0))) {
          $telephone = HTML::sanitize($_POST['telephone']);
        }

        if ((($CLICSHOPPING_Customer->getCustomersGroupID() == 0) && (ACCOUNT_CELLULAR_PHONE =='true')) || (($CLICSHOPPING_Customer->getCustomersGroupID() != 0) && (ACCOUNT_CELLULAR_PHONE_PRO =='true'))) {
          $cellular_phone = HTML::sanitize($_POST['cellular_phone']);
        }

        if ((($CLICSHOPPING_Customer->getCustomersGroupID() == 0) && (ACCOUNT_FAX =='true')) || (($CLICSHOPPING_Customer->getCustomersGroupID() != 0) && (ACCOUNT_FAX_PRO =='true'))) {
          $fax = HTML::sanitize($_POST['fax']);
        }

        if (((ACCOUNT_STATE == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_STATE_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
          if (isset($_POST['zone_id'])) {
            $zone_id = HTML::sanitize($_POST['zone_id']);
          } else {
            $zone_id = false;
          }
          $state = HTML::sanitize($_POST['state']);
        }

// Clients B2C et B2B : Controle selection de la civilite
        if ((ACCOUNT_GENDER == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) {
          if ( ($gender != 'm') && ($gender != 'f') ) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_gender_error'), 'error', 'addressbook');
          }
        } else if ((ACCOUNT_GENDER_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0)) {
          if ( ($gender != 'm') && ($gender != 'f') ) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_gender_error_pro'), 'error', 'addressbook');
          }
        }


// Clients B2C et B2B : Controle entree du prenom
        if ((strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_first_name_error', ['min_length' => ENTRY_FIRST_NAME_MIN_LENGTH]), 'error', 'addressbook');

        } else if ((strlen($firstname) < ENTRY_FIRST_NAME_PRO_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_first_name_error_pro', ['min_length' => ENTRY_FIRST_NAME_PRO_MIN_LENGTH]), 'error', 'addressbook');
        }

// Clients B2C et B2B : Controle entree du nom de famille
        if ((strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_last_name_error', ['min_length' => ENTRY_LAST_NAME_MIN_LENGTH]), 'error', 'addressbook');

        } else if ((strlen($lastname) < ENTRY_LAST_NAME_PRO_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_last_name_error_pro', ['min_length' => ENTRY_LAST_NAME_PRO_MIN_LENGTH]), 'error', 'addressbook');
        }

// Clients B2C et B2B : Controle entree adresse
        if ((strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_street_address_error', ['min_length' => ENTRY_STREET_ADDRESS_MIN_LENGTH]), 'error', 'addressbook');
        } else if ((strlen($street_address) < ENTRY_STREET_ADDRESS_PRO_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_street_address_error_pro', ['min_length' => ENTRY_STREET_ADDRESS_PRO_MIN_LENGTH]), 'error', 'addressbook');
        }

// Clients B2C et B2B : Controle entree code postal
        if ((strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_post_code_error', ['min_length' => ENTRY_POSTCODE_MIN_LENGTH]), 'danger', 'addressbook');

        } else if ((strlen($postcode) < ENTRY_POSTCODE_PRO_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_post_code_error_pro', ['min_length' => ENTRY_POSTCODE_PRO_MIN_LENGTH]), 'error', 'addressbook');
        }

// Clients B2C et B2B : Controle entree de la ville
        if ((strlen($city) < ENTRY_CITY_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_city_error', ['min_length' => ENTRY_CITY_MIN_LENGTH]), 'error', 'addressbook');
        } else if ((strlen($city) < ENTRY_CITY_PRO_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_city_error_pro', ['min_length' => ENTRY_CITY_PRO_MIN_LENGTH]), 'error', 'addressbook');
        }

// Clients B2C et B2B : Controle de la selection du pays
        if ((!is_numeric($country)) && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_country_error'), 'error', 'addressbook');

        } else if ((!is_numeric($country)) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_country_error_pro'), 'error', 'addressbook');
        }

// Clients B2C et B2B : Controle entree du departement
        if (((ACCOUNT_STATE == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_STATE_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
          $zone_id = 0;

          $Qcheck = $CLICSHOPPING_Db->prepare('select zone_country_id
                                         from :table_zones
                                         where zone_country_id = :zone_country_id
                                         and zone_status = 0
                                         limit 1
                                         ');
          $Qcheck->bindInt(':zone_country_id', (int)$country);
          $Qcheck->execute();

          $entry_state_has_zones = ($Qcheck->fetch() !== false);

          if ($entry_state_has_zones === true) {

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

            $result = $Qzone->fetchAll();

            if ( count($result) === 1 ) {
              $zone_id = (int)$result[0]['zone_id'];
            } else {
              $error = true;
              if ($CLICSHOPPING_Customer->getCustomersGroupID() == 0) {
                $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_state_error_select'), 'error', 'addressbook');

              } else if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
                $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_state_error_select_pro'), 'error', 'addressbook');
              }
            } // end else
          } // end $entry_state_has_zones
        } else {
          if ((strlen($state) < ENTRY_STATE_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_state_error', ['min_length' => ENTRY_STATE_MIN_LENGTH]), 'error', 'addressbook');

          } else if ((strlen($state) < ENTRY_STATE_PRO_MIN_LENGTH) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0)) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_state_error_select_pro', ['min_length' => entry_state_error_select_pro]), 'error', 'addressbook');
          }
        } // end else

        if ($error === false) {

          $sql_data_array = ['entry_firstname' => $firstname,
                             'entry_lastname' => $lastname,
                             'entry_street_address' => $street_address,
                             'entry_postcode' => $postcode,
                             'entry_city' => $city,
                             'entry_country_id' => (int)$country
                            ];

          if (((ACCOUNT_GENDER == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_GENDER_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
            $sql_data_array['entry_gender'] = $gender;
          }

          if (((ACCOUNT_COMPANY == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_COMPANY_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
            $sql_data_array['entry_company'] = $company;
          }

          if (((ACCOUNT_SUBURB == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_SUBURB_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
            $sql_data_array['entry_suburb'] = $suburb;
          }

          if (((ACCOUNT_STATE == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_STATE_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {

            if ($zone_id > 0) {
              $sql_data_array['entry_zone_id'] = (int)$zone_id;
              $sql_data_array['entry_state'] = '';
            } else {
              $sql_data_array['entry_zone_id'] = '0';
              $sql_data_array['entry_state'] = $state;
            }
          }

          if ( AddressBook::checkEntry($_GET['edit']) !== false ) {
            $CLICSHOPPING_Db->save('address_book', $sql_data_array, ['address_book_id' => $_GET['edit'],
                                                              'customers_id' => (int)$CLICSHOPPING_Customer->getID()
                                                             ]
                            );

// register session variables
            if ( (isset($_POST['primary']) && ($_POST['primary'] == 'on')) || ($_GET['edit'] == $CLICSHOPPING_Customer->getDefaultAddressID()) ) {
              $CLICSHOPPING_Customer->setCountryID($country);
              $CLICSHOPPING_Customer->setZoneID(($zone_id > 0) ? (int)$zone_id : '0');

              $CLICSHOPPING_Customer->setDefaultAddressID($_GET['edit']);
            }

            if ($_POST['shopping'] != 1) {
              $sql_data_array = ['customers_firstname' => $firstname,
                                 'customers_lastname' => $lastname
                                ];

              if (((ACCOUNT_GENDER == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_GENDER_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
                $sql_data_array['customers_gender'] = $gender;
              }

            } else {
              $sql_data_array = ['customers_firstname' => $firstname,
                                 'customers_lastname' => $lastname,
                                 'customers_telephone' => $telephone
                                ];

              if (((ACCOUNT_GENDER == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_GENDER_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
                $sql_data_array['customers_gender'] = $gender;
              }

              if (((ACCOUNT_CELLULAR_PHONE == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_CELLULAR_PHONE_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
                $sql_data_array['customers_cellular_phone'] = $cellular_phone;
              }

              if (((ACCOUNT_FAX == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_FAX_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
                $sql_data_array['customers_fax'] = $fax;
              }
            }

            $CLICSHOPPING_Db->save('customers', $sql_data_array, array('customers_id' => (int)$CLICSHOPPING_Customer->getID()));

            $CLICSHOPPING_Hooks->call('AddressBookProcess', 'Edit');
          } // end $Qcheck->fetch

          if ($_POST['shopping'] == 1) {
            CLICSHOPPING::redirect('index.php', 'Cart');
          } else {
            CLICSHOPPING::redirect('index.php','Account&AddressBook');
          }
        }
      }
    }
  }