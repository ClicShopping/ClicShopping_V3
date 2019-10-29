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


  namespace ClicShopping\Apps\Customers\Customers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Customers;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Is;

  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\ClicShoppingAdmin\TemplateEmailAdmin;

  class Create extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {
      $CLICSHOPPING_Customers = Registry::get('Customers');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Mail = Registry::get('Mail');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

      $error = false;

      if (isset($_POST['customers_gender'])) {
        $customers_gender = HTML::sanitize($_POST['customers_gender']);
      } else {
        $error = true;
      }

// Informations client
      if (isset($_POST['customers_firstname'])) $customers_firstname = HTML::sanitize($_POST['customers_firstname']);
      if (isset($_POST['customers_lastname'])) $customers_lastname = HTML::sanitize($_POST['customers_lastname']);
      if (isset($_POST['customers_telephone'])) $customers_telephone = HTML::sanitize($_POST['customers_telephone']);
      if (isset($_POST['customers_fax'])) $customers_fax = HTML::sanitize($_POST['customers_fax']);
      if (isset($_POST['customers_cellular_phone'])) $customers_cellular_phone = HTML::sanitize($_POST['customers_cellular_phone']);
      if (isset($_POST['customers_languages_id'])) $customers_languages_id = HTML::sanitize($_POST['customers_languages_id']);
      if (isset($_POST['customers_dob'])) $customers_dob = HTML::sanitize($_POST['customers_dob']);
      if (isset($_POST['customers_group_id'])) $customers_group_id = HTML::sanitize($_POST['customers_group_id']);

      $dobDateTime = new DateTime($customers_dob, false);

// Informations sur la societe
      if (isset($_POST['customers_company'])) $customers_company = HTML::sanitize($_POST['customers_company']);
      if (isset($_POST['customers_siret'])) $customers_siret = HTML::sanitize($_POST['customers_siret']);
      if (isset($_POST['customers_tva_intracom_code_iso'])) $customers_ape = HTML::sanitize($_POST['customers_ape']);

// Informations numero de TVA avec transformation de code ISO en majuscule
      if (isset($_POST['customers_tva_intracom_code_iso'])) $customers_tva_intracom_code_iso = HTML::sanitize($_POST['customers_tva_intracom_code_iso']);
      $customers_tva_intracom_code_iso = strtoupper($customers_tva_intracom_code_iso);

      if (isset($_POST['customers_tva_intracom'])) $customers_tva_intracom = HTML::sanitize($_POST['customers_tva_intracom']);

// Informations sur le type de facturation
      if (isset($_POST['customers_street_address'])) $customers_street_address = HTML::sanitize($_POST['customers_street_address']);
      if (isset($_POST['customers_suburb'])) $customers_suburb = HTML::sanitize($_POST['customers_suburb']);
      if (isset($_POST['postcode'])) $customers_postcode = HTML::sanitize($_POST['postcode']);
      if (isset($_POST['city'])) $customers_city = HTML::sanitize($_POST['city']);
      if (isset($_POST['country'])) $customers_country_id = HTML::sanitize($_POST['country']);
      if (isset($_POST['customers_company'])) $customers_company = HTML::sanitize($_POST['customers_company']);
      if (isset($_POST['state'])) $customers_state = HTML::sanitize($_POST['state']);

// Autorisation aux clients de modifier Les informations de la societe
      if (isset($_POST['customers_modify_company'])) $customers_modify_company = HTML::sanitize($_POST['customers_modify_company']);

// Autorisation aux clients de modifier adresse principal
      if (isset($_POST['customers_modify_address_default'])) $customers_modify_address_default = HTML::sanitize($_POST['customers_modify_address_default']);
      if (isset($_POST['customers_add_address'])) $customers_add_address = HTML::sanitize($_POST['customers_add_address']);

      if (isset($_POST['customers_email'])) $customers_email = HTML::sanitize($_POST['customers_email']);
      if (isset($_POST['customers_email_address'])) $customers_email_address = $_POST['customers_email_address'];

// Information sur la zone geographique
      $customers_zone_id = false;

      if (ACCOUNT_STATE_PRO == 'true' || ACCOUNT_STATE == 'true') {
        if (isset($_POST['zone_id'])) {
          $customers_zone_id = HTML::sanitize($_POST['customers_zone_id']);
        }
      }

      $QmultipleGroups = $CLICSHOPPING_Customers->db->prepare('select distinct customers_group_id
                                                                 from :table_products_groups
                                                                ');

      $QmultipleGroups->execute();

      while ($QmultipleGroups->fetch()) {
        $Qmultiplecustomers = $CLICSHOPPING_Customers->db->prepare('select distinct customers_group_id
                                                                      from :table_customers_groups
                                                                      where customers_group_id = :customers_group_id
                                                                     ');

        $Qmultiplecustomers->bindInt(':customers_group_id', $QmultipleGroups->valueInt('customers_group_id'));
        $Qmultiplecustomers->execute();

        if (!$Qmultiplecustomers->fetch()) {
          $Qdelete = $CLICSHOPPING_Customers->db->prepare('delete 
                                                             from :table_products_groups
                                                             where customers_group_id = :customers_group_id
                                                           ');
          $Qdelete->bindInt(':customers_group_id', $QmultipleGroups->valueInt('customers_group_id'));
          $Qdelete->execute();
        }
      } // end while

// Controle des saisies faites sur les champs TVA Intracom
      if ((strlen($customers_tva_intracom_code_iso) > 0) || (strlen($customers_tva_intracom) > 0)) {
        $QcustomersTva = $CLICSHOPPING_Customers->db->prepare('select countries_iso_code_2
                                                                 from :table_countries
                                                                 where countries_iso_code_2 = :countries_iso_code_2
                                                               ');

        $QcustomersTva->bindValue(':countries_iso_code_2', $customers_tva_intracom_code_iso);

        $QcustomersTva->execute();

        if ($QcustomersTva->fetch()) {
          $error = false;
        } else {
          $error = true;
          $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Customers->getDef('error_iso'), 'error', 'head');
        }
      }

      if (strlen($customers_firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
        $error = true;
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Customers->getDef('error_firstname'), 'error', 'head');
      }

      if (strlen($customers_lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
        $error = true;
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Customers->getDef('error_lastname'), 'error', 'head');
      }

      if (!Is::EmailAddress($customers_email_address)) {
        $error = true;
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Customers->getDef('error_email'), 'error', 'head');
      }

      if (strlen($customers_street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
        $error = true;
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Customers->getDef('error_address'), 'error', 'head');
      }

      if (strlen($customers_postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
        $error = true;
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Customers->getDef('error_postcode'), 'error', 'head');
      }

      if (strlen($customers_city) < ENTRY_CITY_MIN_LENGTH) {
        $error = true;
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Customers->getDef('error_city'), 'error', 'head');
      }


      $entry_zone_id = 0;

      if (empty($customers_country_id)) {
        $error = true;
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Customers->getDef('entry_state_error_select'), 'error', 'header');
      } else {
        $Qcheck = $CLICSHOPPING_Customers->db->get('zones', 'zone_country_id', ['zone_country_id' => (int)$customers_country_id]);

        if ($Qcheck->ValueInt('zone_country_id') != 0) {
          $entry_zone_id = $Qcheck->ValueInt('zone_country_id');
        }

        if (isset($_SESSION['entry_state_has_zones']) === true) {
          if (ACCOUNT_STATE_DROPDOWN == 'true') {
            $Qzone = $CLICSHOPPING_Customers->db->prepare('select distinct zone_id
                                                     from :table_zones
                                                     where zone_country_id = :zone_country_id
                                                     and zone_id = :zone_id
                                                     and zone_status = 0
                                                   ');

            $Qzone->bindInt(':zone_country_id', $customers_country_id);
            $Qzone->bindInt(':zone_id', $customers_state);
            $Qzone->execute();
          } elseif (ACCOUNT_STATE == 'true') {
            if (!is_numeric($customers_state)) {
              $Qzone = $CLICSHOPPING_Customers->db->prepare('select distinct zone_id
                                                              from :table_zones
                                                              where zone_country_id = :zone_country_id
                                                              and zone_name = :zone_name
                                                              and zone_status = 0
                                                            ');
              $Qzone->bindInt(':zone_country_id', $customers_country_id);
              $Qzone->bindValue(':zone_name', $customers_state);

              $Qzone->execute();
            } else {
              $Qzone = $CLICSHOPPING_Customers->db->prepare('select distinct zone_id
                                                              from :table_zones
                                                              where zone_country_id = :zone_country_id
                                                              and zone_id = :zone_id
                                                              and zone_status = 0
                                                            ');
              $Qzone->bindInt(':zone_country_id', $customers_country_id);
              $Qzone->bindValue(':zone_id', $customers_state);

              $Qzone->execute();
            }
          }

          if (!empty($Qzone->valueInt('zone_id')) || !is_null($Qzone->valueInt('zone_id'))) {
            $entry_zone_id = (int)$Qzone->valueInt('zone_id');
          } else {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_state_error_select_pro'), 'error', 'header');
          }
        } else {
          if (strlen($customers_state) < ENTRY_STATE_MIN_LENGTH) {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_state_error_pro', ['min_length' => ENTRY_STATE_MIN_LENGTH]), 'error', 'header');
          }
        }
      }

      if (strlen($customers_telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
        $error = true;
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Customers->getDef('error_telephone'), 'error', 'header');
      }

      $Qcheck = $CLICSHOPPING_Customers->db->prepare('select customers_email_address
                                                       from :table_customers
                                                       where customers_email_address = :customers_email_address
                                                       ');
      $Qcheck->bindValue(':customers_email_address', $customers_email_address);
      $Qcheck->execute();


      if ($Qcheck->fetch() !== false) {
        $error = true;
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Customers->getDef('error_email_address_exist'), 'error', 'header');
      }

      if ($error === false) {
        $customers_password = 'clicshopping_' . rand(5, 500);

// Autorisation aux clients de modifier informations societe et adresse principal + Ajout adresse
        if ($customers_modify_company != '1') $customers_modify_company = '0';
        if ($customers_modify_address_default != '1') $customers_modify_address_default = '0';
        if ($customers_add_address != '1') $customers_add_address = '0';

        $sql_data_array = ['customers_company' => $customers_company,
          'customers_siret' => $customers_siret,
          'customers_ape' => $customers_ape,
          'customers_tva_intracom' => $customers_tva_intracom,
          'customers_tva_intracom_code_iso' => $customers_tva_intracom_code_iso,
          'customers_gender' => $customers_gender,
          'customers_firstname' => $customers_firstname,
          'customers_lastname' => $customers_lastname,
          'customers_dob' => $dobDateTime->getRaw(false),
          'customers_email_address' => $customers_email_address,
          'customers_telephone' => $customers_telephone,
          'customers_fax' => $customers_fax,
          'customers_password' => $customers_password,
          'customers_newsletter' => 1,
          'languages_id' => (int)$customers_languages_id,
          'customers_group_id' => (int)$customers_group_id,
          'member_level' => 1,
          'customers_modify_company' => $customers_modify_company,
          'customers_modify_address_default' => $customers_modify_address_default,
          'customers_add_address' => $customers_add_address,
          'customers_cellular_phone' => $customers_cellular_phone,
        ];

        $CLICSHOPPING_Customers->db->save('customers', $sql_data_array);

        $customer_id = $CLICSHOPPING_Customers->db->lastInsertId();

//zone
        if ($customers_zone_id > 0) $customers_state = '';

        $sql_data_array = ['customers_id' => (int)$customer_id,
          'entry_gender' => $customers_gender,
          'entry_company' => $customers_company,
          'entry_firstname' => $customers_firstname,
          'entry_lastname' => $customers_lastname,
          'entry_street_address' => $customers_street_address,
          'entry_suburb' => $customers_suburb,
          'entry_postcode' => $customers_postcode,
          'entry_city' => $customers_city,
          'entry_country_id' => (int)$customers_country_id,
          'entry_siret' => $customers_siret,
          'entry_ape' => $customers_ape,
          'entry_tva_intracom' => $customers_tva_intracom
        ];


        if (ACCOUNT_STATE == 'true') {
          if (isset($entry_zone_id) && $entry_zone_id > 0) $entry_state = '';
          $sql_data_array['entry_zone_id'] = $entry_zone_id;
          $sql_data_array['entry_state'] = $customers_state;
        } else {
          $sql_data_array['entry_zone_id'] = 0;
          $sql_data_array['entry_state'] = $customers_state;
        }
      } else {
        $CLICSHOPPING_Customers->redirect('Create', 'error=' . $error);
      }

      $CLICSHOPPING_Customers->db->save('address_book', $sql_data_array);

      $address_id = $CLICSHOPPING_Customers->db->lastInsertId();

      $sql_data_array['customers_info_id'] = (int)$customer_id;

      $Qupdate = $CLICSHOPPING_Customers->db->prepare('update :table_customers
                                                        set customers_default_address_id = :customers_default_address_id
                                                        where customers_id = :customers_id
                                                      ');
      $Qupdate->bindInt(':customers_default_address_id', $address_id);
      $Qupdate->bindInt(':customers_id', $customer_id);
      $Qupdate->execute();

      $CLICSHOPPING_Customers->db->save('customers_info', [
          'customers_info_id' => (int)$customer_id,
          'customers_info_number_of_logons' => 0,
          'customers_info_date_account_created' => 'now()'
        ]
      );

      $template_email_welcome_admin = TemplateEmailAdmin::getTemplateEmailWelcomeAdmin();
      $template_email_signature = TemplateEmailAdmin::getTemplateEmailSignature();
      $template_email_footer = TemplateEmailAdmin::getTemplateEmailTextFooter();

      $email_subject = html_entity_decode(CLICSHOPPING::getDef('email_subject', ['store_name' => STORE_NAME]));

      if (ACCOUNT_GENDER == 'true') {
        if ($customers_gender == 'm') {
          $email_gender = CLICSHOPPING::getDef('email_greet_mr', ['greet_mr' => HTML::sanitize($_POST['customers_firstname']) . ' ' . HTML::sanitize($_POST['customers_lastname'])]);
        } else {
          $email_gender = CLICSHOPPING::getDef('email_greet_ms', ['greet_ms' => HTML::sanitize($_POST['customers_firstname']) . ' ' . HTML::sanitize($_POST['customers_lastname'])]);
        }
      } else {
        $email_gender = CLICSHOPPING::getDef('email_greet_none', ['greet_none' => HTML::sanitize($_POST['customers_firstname']) . ' ' . HTML::sanitize($_POST['customers_lastname'])]);
      }

      $email_text = $email_gender . '<br /><br />' . $template_email_welcome_admin . '<br /><br />' . $template_email_signature . '<br /><br />' . $template_email_footer;

// Envoi du mail avec gestion des images pour Fckeditor et Imanager.
      if ($customers_email == '1') {
        $message = html_entity_decode($email_text);
        $message = str_replace('src="/', 'src="' . HTTP::getShopUrlDomain(), $message);
        $CLICSHOPPING_Mail->addHtmlCkeditor($message);
        $CLICSHOPPING_Mail->build_message();
        $from = STORE_OWNER_EMAIL_ADDRESS;

        $name = $email_gender . ' ' . $customers_firstname . ' ' . $customers_lastname;

        $CLICSHOPPING_Mail->send($name, $customers_email_address, '', $from, $email_subject);
      }

      $CLICSHOPPING_Hooks->call('Customers', 'Create');

      $CLICSHOPPING_Customers->redirect('Customers', 'page=' . $page . 'error=' . $error);
    }
  }