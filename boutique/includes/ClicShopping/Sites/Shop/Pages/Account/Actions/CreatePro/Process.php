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

  namespace ClicShopping\Sites\Shop\Pages\Account\Actions\CreatePro;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\Is;
  use ClicShopping\OM\Hash;

  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\Shop\TemplateEmail;

  class Process extends \ClicShopping\OM\PagesActionsAbstract  {

    public function execute()  {
      global $process, $entry_state_has_zones, $country;

      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');
      $CLICSHOPPING_Mail = Registry::get('Mail');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
        $error = false;
        $process = true;
        $zone_id = false;

        $CLICSHOPPING_Hooks->call('CreatePro','PreAction');

        $firstname = HTML::sanitize($_POST['firstname']);
        $lastname = HTML::sanitize($_POST['lastname']);
        $email_address = HTML::sanitize($_POST['email_address']);
        $email_address_confirmation = HTML::sanitize($_POST['email_address_confirm']);
        $postcode = HTML::sanitize($_POST['postcode']);
        $city = HTML::sanitize($_POST['city']);
        $customer_website_company = HTML::sanitize($_POST['customer_website_company']);
        $street_address = HTML::sanitize($_POST['street_address']);

        if (ACCOUNT_DOB_PRO == 'true') $dob = HTML::sanitize($_POST['dob']);
        if (ACCOUNT_GENDER_PRO == 'true') $gender = HTML::sanitize($_POST['gender']);
        if (ACCOUNT_COMPANY_PRO == 'true') $company = HTML::sanitize($_POST['company']);
        if (ACCOUNT_SIRET_PRO == 'true') $siret = HTML::sanitize($_POST['siret']);
        if (ACCOUNT_APE_PRO == 'true') $ape = HTML::sanitize($_POST['ape']);
        if (ACCOUNT_TVA_INTRACOM_PRO == 'true') $tva_intracom = HTML::sanitize($_POST['tva_intracom']);
        if (ACCOUNT_TVA_INTRACOM_PRO == 'true') $iso = HTML::sanitize($_POST['ISO']);

        if (ACCOUNT_SUBURB_PRO == 'true') $suburb = HTML::sanitize($_POST['suburb']);

        if (ACCOUNT_STATE_PRO == 'true') {
          $state = HTML::sanitize($_POST['state']);
          if (isset($_POST['zone_id'])) {
            $zone_id = HTML::sanitize($_POST['zone_id']);
          }
        }

        $country = HTML::sanitize($_POST['country']);
        $telephone = HTML::sanitize($_POST['telephone']);

        if (ACCOUNT_CELLULAR_PHONE_PRO == 'true') $cellular_phone = HTML::sanitize($_POST['cellular_phone']);
        if (ACCOUNT_FAX_PRO == 'true')     $fax = HTML::sanitize($_POST['fax']);

        $newsletter = HTML::sanitize($_POST['newsletter']);
        $password = HTML::sanitize($_POST['password']);
        $confirmation = HTML::sanitize($_POST['confirmation']);
        $customer_agree_privacy = HTML::sanitize($_POST['customer_agree_privacy']);
          $antispam = HTML::sanitize($_POST['antispam']);

// simple Recaptcha
        if (!Is::ValidateAntiSpam((int)$antispam) && CONFIG_ANTISPAM == 'simple') {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_email_address_check_error_number'), 'error', 'create_account_pro');
        }

// Recaptcha
        if (defined('MODULES_HEADER_TAGS_GOOGLE_RECAPTCHA_CREATE_ACCOUNT_PRO') CONFIG_ANTISPAM == 'recaptcha')
          if (MODULES_HEADER_TAGS_GOOGLE_RECAPTCHA_CREATE_ACCOUNT_PRO == 'True') {
            $error = $CLICSHOPPING_Hooks->call('AllShop', 'GoogleRecaptchaProcess');
          }
        }

        if (DISPLAY_PRIVACY_CONDITIONS == 'true') {
          if ($customer_agree_privacy != 'on') {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_agreement_check_error'), 'error', 'create_account_pro');
          }
        }

// Clients B2B : Controle entree de la societe
        if (ACCOUNT_COMPANY_PRO == 'true') {
          if (strlen($company) < ENTRY_COMPANY_PRO_MIN_LENGTH) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_company_error_pro', ['min_length' => ENTRY_COMPANY_PRO_MIN_LENGTH]), 'error', 'create_account_pro');
          }
        }

// Clients B2B : Controle entree numero de siret
        if (ACCOUNT_SIRET_PRO == 'true') {
          if (strlen($siret) < ENTRY_SIRET_MIN_LENGTH) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_siret_error', ['min_length' => ENTRY_SIRET_MIN_LENGTH]), 'error', 'create_account_pro');
          }
        }

// Clients B2B : Controle entree code APE
        if (ACCOUNT_APE_PRO == 'true') {
          if (strlen($ape) < ENTRY_CODE_APE_MIN_LENGTH) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_code_ape_error', ['min_length' => ENTRY_CODE_APE_MIN_LENGTH]), 'error', 'create_account_pro');
          }
        }

// Clients B2B : Controle entree numero de TVA Intracom
        if (ACCOUNT_TVA_INTRACOM_PRO == 'true') {
          if (strlen($tva_intracom) < ENTRY_TVA_INTRACOM_MIN_LENGTH) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_tva_intracom_error', ['min_length' => ENTRY_TVA_INTRACOM_MIN_LENGTH]), 'danger', 'create_account_pro');
          }
        }

// Clients B2C : Controle selection de la civilite
        if (ACCOUNT_GENDER_PRO == 'true') {
          if ( ($gender != 'm') && ($gender != 'f') ) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_gender_error_pro'), 'danger', 'create_account_pro');
          }
        }

// Clients B2C : Controle entree du prenom
        if (strlen($firstname) < ENTRY_FIRST_NAME_PRO_MIN_LENGTH) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_first_name_error_pro', ['min_length' => ENTRY_FIRST_NAME_PRO_MIN_LENGTH]), 'danger', 'create_account_pro');
        }

// Clients B2C : Controle entree du nom de famille
        if (strlen($lastname) < ENTRY_LAST_NAME_PRO_MIN_LENGTH) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_last_name_error_pro', ['min_length' => ENTRY_LAST_NAME_PRO_MIN_LENGTH]), 'danger', 'create_account_pro');
        }

// Clients B2C : Controle entree date de naissance
        if (ACCOUNT_DOB_PRO == 'true') {
          $dobDateTime = new DateTime($dob);

          if ((strlen($dob) < ENTRY_DOB_MIN_LENGTH) || ($dobDateTime->isValid() === false)) {

            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_date_of_birth_error_pro', ['min_length' => ENTRY_DOB_MIN_LENGTH]), 'danger', 'create_account_pro');
          }
        }

// Clients B2C : Controle entree adresse e-mail
        if (Is::email($email_address) === false) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_email_address_check_error_pro'), 'danger', 'create_account_pro');

        } elseif ($email_address != $email_address_confirmation) {
          $error = true;
          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_email_address_confirmation_check_error_pro'), 'danger', 'create_account_pro');
        } else {

          $Qcheckemail = $CLICSHOPPING_Db->prepare('select customers_id
                                                    from :table_customers
                                                    where customers_email_address = :customers_email_address
                                                   ');
          $Qcheckemail->bindValue(':customers_email_address', $email_address);

          $Qcheckemail->execute();

          if ($Qcheckemail->fetch() !== false) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_email_address_error_exists_pro'), 'danger', 'create_account_pro');
          }
        }

        if (strlen($street_address) < ENTRY_STREET_ADDRESS_PRO_MIN_LENGTH) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_street_address_error_pro', ['min_length' => ENTRY_STREET_ADDRESS_PRO_MIN_LENGTH]), 'danger', 'create_account_pro');
        }

        if (strlen($postcode) < ENTRY_POSTCODE_PRO_MIN_LENGTH) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_post_code_error_pro', ['min_length' => ENTRY_POSTCODE_PRO_MIN_LENGTH]), 'danger', 'create_account_pro');
        }

        if (strlen($city) < ENTRY_CITY_PRO_MIN_LENGTH) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_city_error', ['min_length' => ENTRY_CITY_PRO_MIN_LENGTH]), 'danger', 'create_account_pro');
        }

        if (!is_numeric($country)) {
          $Qcheck = $CLICSHOPPING_Db->prepare('select countries_id
                                               from :table_countries
                                               where countries_iso_code_2 = :countries_iso_code_2
                                              ');
          $Qcheck->bindValue(':countries_iso_code_2', $country);
          $Qcheck->execute();

          $country = $Qcheck->valueInt('countries_id');
        } else {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_county_error_pro'), 'danger', 'create_account_pro');
        }

        if (ACCOUNT_STATE_PRO == 'true') {

          $zone_id = 0;

          $Qcheck = $CLICSHOPPING_Db->prepare('select count(*) as total
                                               from :table_zones
                                               where zone_country_id = :zone_country_id
                                              ');
          $Qcheck->bindInt(':zone_country_id', (int)$country);
          $Qcheck->execute();

          $entry_state_has_zones = ($Qcheck->valueInt('total') > 0);

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
              if (!is_numeric($state)) {
                $Qzone = $CLICSHOPPING_Db->prepare('select distinct zone_id
                                                    from :table_zones
                                                    where zone_country_id = :zone_country_id
                                                    and zone_name = :zone_name
                                                    and zone_status = 0
                                                  ');
                $Qzone->bindInt(':zone_country_id', $country); // 73
                $Qzone->bindValue(':zone_name',  $state);

                $Qzone->execute();
              } else {
                $Qzone = $CLICSHOPPING_Db->prepare('select distinct zone_id
                                                  from :table_zones
                                                  where zone_country_id = :zone_country_id
                                                  and zone_id = :zone_id
                                                  and zone_status = 0
                                                ');
                $Qzone->bindInt(':zone_country_id', $country); // 73
                $Qzone->bindValue(':zone_id',  $state);

                $Qzone->execute();
              }
            }

            if (count($Qzone->fetchAll()) == 1) {
              $zone_id = (int)$Qzone->result[0]['zone_id'];
            } else {
              $error = true;

              $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_state_error_select_pro'), 'error', 'create_account_pro');
            }
          } else {
            if (strlen($state) < ENTRY_STATE_PRO_MIN_LENGTH) {
              $error = true;

              $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_state_error_pro', ['min_length' => ENTRY_STATE_PRO_MIN_LENGTH]), 'danger', 'create_account_pro');
            }
          }
        }

        if (strlen($telephone) < ENTRY_TELEPHONE_PRO_MIN_LENGTH) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_telephone_number_error_pro', ['min_length' => ENTRY_TELEPHONE_PRO_MIN_LENGTH]), 'danger', 'create_account_pro');
        }

// Clients B2B : Controle entree du mot de passe selon si l'approbation des membres est sur false
        if (MEMBER == 'false'){
          if (strlen($password) < ENTRY_PASSWORD_PRO_MIN_LENGTH) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_password_error_pro', ['min_length' => ENTRY_PASSWORD_PRO_MIN_LENGTH]), 'danger', 'create_account_pro');

          } elseif ($password != $confirmation) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_password_error_not_matching_pro'), 'danger', 'create_account_pro');
          }
        }

// Groupe par defaut e utiliser pour les nouveaux clients
        $QcustomersGroup = $CLICSHOPPING_Db->prepare('select group_order_taxe,
                                                       group_payment_unallowed,
                                                       group_shipping_unallowed
                                                 from :table_customers_groups
                                                 where customers_group_id = :customers_group_id
                                                 ');
        $QcustomersGroup->bindInt(':customers_group_id', (int)ACCOUNT_GROUP_DEFAULT_PRO);
        $QcustomersGroup->execute();

// Groupe par defaut e utiliser pour les nouveaux clients

        if ($QcustomersGroup->fetch() !== false) {
          $customers_group =  $QcustomersGroup->fetch();
        }

// Controle si le compte doit etre valide selon la configuration d'approbation des membres
        if (MEMBER == 'false'){
          $member_level_approbation = 1;
        } else {
          $member_level_approbation = 0;
        }

// Autorisation par defaut au client de pouvoir modifier les informations sur la societe
        if (ACCOUNT_MODIFY_PRO == 'false') {
          $customers_modify_company = 0;
        } else {
          $customers_modify_company = 1;
        }

// Autorisation par defaut au client de pouvoir modifier l'adresse principale
        if (ACCOUNT_MODIFY_ADRESS_DEFAULT_PRO == 'false') {
          $customers_modify_address_default = 0;
        } else {
          $customers_modify_address_default = 1;
        }

// Autorisation par defaut au client de pouvoir ajouter des adresses dans son carnet
        if (ACCOUNT_ADRESS_BOOK_PRO == 'false') {
          $customers_add_address = 0;
        } else {
          $customers_add_address = 1;
        }

        if ( $error === false ) {

// Enregistrement des informations du client dans la base de donnees
// member_level sur 1 permet d'eviter d'avoir e faire une approbation pour des clients normaux
          $sql_data_array = ['customers_firstname' => $firstname,
                              'customers_lastname' => $lastname,
                              'customers_email_address' => $email_address,
                              'customers_telephone' => $telephone,
                              'customers_newsletter' => $newsletter,
                              'customers_password' => Hash::encrypt($password),
                              'languages_id' => (int)$CLICSHOPPING_Language->getId(),
                              'member_level' => $member_level_approbation,
                              'customers_modify_company' => $customers_modify_company,
                              'customers_modify_address_default' => $customers_modify_address_default,
                              'customers_add_address' => $customers_add_address,
                              'client_computer_ip' => HTTP::getIPAddress(),
                              'provider_name_client' => HTTP::getProviderNameCustomer(),
                              'customer_website_company' => $customer_website_company
                            ];

          if (ACCOUNT_CELLULAR_PHONE_PRO == 'true')  $sql_data_array['customers_cellular_phone'] = $cellular_phone;
          if (ACCOUNT_FAX_PRO == 'true')  $sql_data_array['customers_fax'] = $fax;

          if ($QcustomersGroup->fetch() !== false) $sql_data_array['customers_group_id'] = ACCOUNT_GROUP_DEFAULT_PRO;
          if ($QcustomersGroup->fetch() !== false) $sql_data_array['customers_options_order_taxe'] = $customers_group['group_order_taxe'];

          if (ACCOUNT_GENDER_PRO == 'true') $sql_data_array['customers_gender'] = $gender;
          if (ACCOUNT_DOB_PRO == 'true') $sql_data_array['customers_dob'] = $dobDateTime->getRaw(false);
          if (ACCOUNT_COMPANY_PRO == 'true') $sql_data_array['customers_company'] = $company;
          if (ACCOUNT_SIRET_PRO == 'true') $sql_data_array['customers_siret'] = $siret;
          if (ACCOUNT_APE_PRO == 'true') $sql_data_array['customers_ape'] = $ape;
          if (ACCOUNT_TVA_INTRACOM_PRO == 'true') $sql_data_array['customers_tva_intracom'] = $tva_intracom;

          if (ACCOUNT_TVA_INTRACOM_PRO == 'true') $sql_data_array['customers_tva_intracom_code_iso'] = $iso;

          $CLICSHOPPING_Db->save('customers', $sql_data_array);

          $customer_id = $CLICSHOPPING_Db->lastInsertId();
// save element in address book
          $sql_data_array = ['customers_id' => (int)$customer_id,
                              'entry_firstname' => $firstname,
                              'entry_lastname' => $lastname,
                              'entry_street_address' => $street_address,
                              'entry_postcode' => $postcode,
                              'entry_city' => $city,
                              'entry_country_id' => (int)$country
                            ];

          if (ACCOUNT_GENDER_PRO == 'true') $sql_data_array['entry_gender'] = $gender;
          if (ACCOUNT_COMPANY_PRO == 'true') $sql_data_array['entry_company'] = $company;
          if (ACCOUNT_SUBURB_PRO == 'true') $sql_data_array['entry_suburb'] = $suburb;
          if (ACCOUNT_STATE_PRO == 'true') {
            if ($zone_id > 0) {
              $sql_data_array['entry_zone_id'] = (int)$zone_id;
              $sql_data_array['entry_state'] = '';
            } else {
              $sql_data_array['entry_zone_id'] = 0;
              $sql_data_array['entry_state'] = $state;
            }
          }

          $CLICSHOPPING_Db->save('address_book', $sql_data_array);

          $address_id = $CLICSHOPPING_Db->lastInsertId();

          $CLICSHOPPING_Db->save('customers', array('customers_default_address_id' => (int)$address_id),
                                              array('customers_id' => (int)$customer_id)
                                );

          $sql_array = ['customers_info_id' => (int)$customer_id,
                        'customers_info_number_of_logons' => 0,
                        'customers_info_date_account_created' => 'now()'
                        ];

          $CLICSHOPPING_Db->save('customers_info', $sql_array);


// Ouverture de la session si l'approbation n'est pas obligatoire
          if (MEMBER == 'false') {
            $CLICSHOPPING_Customer->setData($customer_id);
          }

          Registry::get('Session')->recreate();

// restore cart contents
          $CLICSHOPPING_ShoppingCart->restore_contents();

// build the message content
          $name = $firstname . ' ' . $lastname;

          if (ACCOUNT_GENDER_PRO == 'true') {
            if ($gender == 'm') {
              $email_gender = sprintf(CLICSHOPPING::getDef('email_greet_mr'), $lastname);
            } else {
              $email_gender = sprintf(CLICSHOPPING::getDef('email_greet_ms'), $lastname);
            }
          } else {
            $email_gender = sprintf(CLICSHOPPING::getDef('email_greet_none'), $firstname);
          }

          if (COUPON_CUSTOMER_B2B != '') {
            $email_coupon = CLICSHOPPING::getDef('email_text_coupon') . ' '. COUPON_CUSTOMER_B2B;
          }

// Envoi des e-mails selon si l'approbation est obligatoire
          if (MEMBER == 'false') {
            $template_email_welcome_catalog = TemplateEmail::getTemplateEmailWelcomeCatalog();
          } else {
            $template_email_welcome_catalog = CLICSHOPPING::getDef('email_welcome');
          }

          if (!empty(COUPON_CUSTOMER)) {
            $email_coupon_catalog = TemplateEmail::getTemplateEmailCouponCatalog();
            $email_coupon = $email_coupon_catalog . COUPON_CUSTOMER;
          }

// Content email
          $template_email_signature = TemplateEmail::getTemplateEmailSignature();
          $template_email_footer = TemplateEmail::getTemplateEmailTextFooter();
          $email_subject = CLICSHOPPING::getDef('email_subject', ['store_name' => STORE_NAME]);
          $email_gender = CLICSHOPPING::getDef('female') . ', '.  CLICSHOPPING::getDef('male') . ' '. $lastname;
          $email_text = $email_gender .',<br /><br />'. $template_email_welcome_catalog .'<br /><br />'. $email_coupon .'<br /><br />' .   $template_email_signature . '<br /><br />' . $template_email_footer;

// EEmail send
          $message = $email_text;
          $message = str_replace('src="/', 'src="' . HTTP::typeUrlDomain() . '/', $message);
          $CLICSHOPPING_Mail->addHtmlCkeditor($message);
          $CLICSHOPPING_Mail->build_message();
          $from = STORE_OWNER_EMAIL_ADDRESS;
          $CLICSHOPPING_Mail->send($name, $email_address, '', $from, $email_subject);

// Administrator email
          if (EMAIL_INFORMA_ACCOUNT_ADMIN == 'true') {
            $email_subject_admin = CLICSHOPPING::getDef('admin_email_subject', ['store_name' => STORE_NAME]);
            $admin_email_welcome = CLICSHOPPING::getDef('admin_email_welcome');

            $data_array = ['customer_name' => $_POST['lastname'],
                           'customer_firstame' => $_POST['firstname'],
                           'customer_company' => $_POST['company'],
                           'customer_mail' => $_POST['email_address']
                          ];

            $admin_email_text_admin = CLICSHOPPING::getDef('admin_email_text', $data_array);

            $email_address = STORE_OWNER_EMAIL_ADDRESS;
            $from = STORE_OWNER_EMAIL_ADDRESS;
            $admin_email_text_admin .= $admin_email_welcome . $admin_email_text_admin;
            $CLICSHOPPING_Mail->addHtmlCkeditor($admin_email_text_admin);
            $CLICSHOPPING_Mail->build_message();
            $CLICSHOPPING_Mail->send(STORE_NAME, $email_address, '', $from, $email_subject_admin);
          }

          $CLICSHOPPING_Hooks->call('Create','Process');

          CLICSHOPPING::redirect('index.php', 'Account&CreatePro&Success');
      }
    }
  }
