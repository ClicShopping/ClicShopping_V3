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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  use ClicShopping\Sites\Shop\AddressBook;

  $CLICSHOPPING_Address = Registry::get('Address');
  $CLICSHOPPING_Customer = Registry::get('Customer');
  $CLICSHOPPING_Db = Registry::get('Db');

  if (!isset($process)) $process = false;
?>
<div class="separator"></div>
    <div class="hr"></div>
    <div class="card">
      <div class="card-header">
        <span class="alert-warning float-md-right"><?php echo CLICSHOPPING::getDef('form_required_information'); ?></span>
        <span><h3><?php echo CLICSHOPPING::getDef('entry_company'); ?></h3></span>
      </div>
      <div class="card-block">
        <div class="card-text">
<?php
// Clients B2C et B2B
// Nouvelle adresse : Affichage du nom societe par defaut si il existe dans la table customers.
// Edition adresse :  Affiche le nom de la societe present dans le carnet d'adresse table address_book.
  if ((($CLICSHOPPING_Customer->getCustomersGroupID() == 0) && (ACCOUNT_COMPANY == 'true')) || (($CLICSHOPPING_Customer->getCustomersGroupID() != 0) && (ACCOUNT_COMPANY_PRO == 'true'))) {

     $QaccountGroup = $CLICSHOPPING_Db->prepare('select customers_company
                                                 from :table_customers
                                                 where customers_id = :customers_id
                                               ');
     $QaccountGroup->bindInt(':customers_id', (int)$CLICSHOPPING_Customer->getID());
     $QaccountGroup->execute();
?>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="InputCompany" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_company'); ?></label>
                <div class="col-sm-6 col-md-4">
<?php
  if (isset($_GET['Edit']) && is_numeric($_GET['edit'])) {
    echo HTML::inputField('company', (isset($entry['company']) ? $entry['company'] : ''), 'id="InputCompany" aria-describedby="' . CLICSHOPPING::getDef('entry_company') . '" placeholder="' . CLICSHOPPING::getDef('entry_company') . '"');
  } else {
    echo HTML::inputField('company', $QaccountGroup->value('customers_company'), 'id="InputCompany" ria-describedby="' . CLICSHOPPING::getDef('entry_company') . '" placeholder="' . CLICSHOPPING::getDef('entry_company') . '"');
  }

  if (($CLICSHOPPING_Customer->getCustomersGroupID() == 0 && ENTRY_COMPANY_MIN_LENGTH > 0) || ($CLICSHOPPING_Customer->getCustomersGroupID() != 0 && ENTRY_COMPANY_PRO_MIN_LENGTH > 0) ) {
    echo '&nbsp;' . (!is_null(CLICSHOPPING::getDef('entry_company_text')) ? '<span class="text-warning">' . CLICSHOPPING::getDef('entry_company_text') . '</span>': '');
  }
?>
                </div>
              </div>
            </div>
          </div>

<?php
  }
?>
        </div>
      </div>
    </div>
    <div class="separator"></div>
    <div class="card">
      <div class="card-header">
        <span class="alert-warning float-md-right"><?php echo CLICSHOPPING::getDef('form_required'); ?></span>
        <span><h3><?php echo CLICSHOPPING::getDef('category_personnal'); ?></h3></span>
      </div>
      <div class="card-block">
        <div class="card-text">
<?php
// Clients B2C et B2B : Informations general - Nom, prenom, date de naissance, email, telephone et fax
  if (((ACCOUNT_GENDER == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_GENDER_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {

    $male = $female = false;
    if (isset($gender)) {
      $male = ($gender == 'm') ? true : false;
      $female = !$male;
    } elseif (isset($entry['gender'])) {
      $male = ($entry['gender'] == 'm') ? true : false;
      $female = !$male;
    }
?>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="gender" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_gender'); ?></label>
                <div class="col-md-5">
                  <?php echo HTML::radioField('gender', 'm', $male) . ' ' . CLICSHOPPING::getDef('male') . '&nbsp;&nbsp;' .  HTML::radioField('gender', 'f', $female) . ' ' . CLICSHOPPING::getDef('female') . '&nbsp;' . (!is_null(CLICSHOPPING::getDef('entry_gender_text')) ? '<span class="text-warning">' . CLICSHOPPING::getDef('entry_gender_text') . '</span>': ''); ?>
                </div>
              </div>
            </div>
          </div>

<?php
  }
?>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="frmNameB" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_first_name'); ?></label>
                <div class="col-sm-6 col-md-4">
<?php
  echo HTML::inputField('firstname', ($CLICSHOPPING_Customer->hasDefaultAddress() ? $CLICSHOPPING_Customer->getFirstName() : null), 'required aria-required="true" id="frmNameB" autocomplete="name" aria-describedby="' . CLICSHOPPING::getDef('entry_first_name') . '" placeholder="' . CLICSHOPPING::getDef('entry_first_name') . '" minlength="'. ENTRY_FIRST_NAME_PRO_MIN_LENGTH .'"');
  if (($CLICSHOPPING_Customer->getCustomersGroupID() == 0 && ENTRY_FIRST_NAME_MIN_LENGTH > 0) || ($CLICSHOPPING_Customer->getCustomersGroupID() != 0 && ENTRY_FIRST_NAME_PRO_MIN_LENGTH > 0 ) ) {
    echo '&nbsp;' . (!is_null(CLICSHOPPING::getDef('entry_first_name_text')) ? '<span class="text-warning">' . CLICSHOPPING::getDef('entry_first_name_text') . '</span>': '');
  }
?>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="InputLastName" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_last_name'); ?></label>
                <div class="col-sm-6 col-md-4">
<?php
  echo HTML::inputField('lastname', ($CLICSHOPPING_Customer->hasDefaultAddress() ? $CLICSHOPPING_Customer->getLastName() : null), 'required aria-required="true" id="frmNameA" autocomplete="name" aria-describedby="' . CLICSHOPPING::getDef('entry_last_name') . '" placeholder="' . CLICSHOPPING::getDef('entry_last_name') . '" minlength="'. ENTRY_LAST_NAME_PRO_MIN_LENGTH .'"');
  if ( ($CLICSHOPPING_Customer->getCustomersGroupID() == 0 && ENTRY_LAST_NAME_MIN_LENGTH > 0) || ($CLICSHOPPING_Customer->getCustomersGroupID() != 0 && ENTRY_LAST_NAME_PRO_MIN_LENGTH > 0 ) ) {
    echo '&nbsp;' . (!is_null(CLICSHOPPING::getDef('entry_last_name_text')) ? '<span class="text-warning">' . CLICSHOPPING::getDef('entry_last_name_text') . '</span>': '');
  }
?>
                </div>
              </div>
            </div>
          </div>
<?php
  if ($_GET['newcustomer'] == 1) {
?>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="frmPhoneNumA" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_telephone_number'); ?></label>
                <div class="col-sm-6 col-md-4">
                  <?php echo HTML::inputField('telephone', null, 'rel="txtTooltipPhone" title="' . CLICSHOPPING::getDef('entry_phone_dgrp') . '" data-toggle="tooltip" data-placement="right" required aria-required="true" id="frmPhoneNumA" autocomplete="tel" aria-describedby="' . CLICSHOPPING::getDef('entry_telephone_number') . '" placeholder="' . CLICSHOPPING::getDef('entry_telephone_number') . '"', 'phone'); ?>
                </div>
              </div>
            </div>
          </div>
<?php
    if (ACCOUNT_CELLULAR_PHONE == 'true') {
?>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="frmPhoneNumB" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_cellular_phone_number'); ?></label>
                <div class="col-sm-6 col-md-4">
                  <?php echo HTML::inputField('cellular_phone', null, 'rel="txtTooltipPhone" title="' . CLICSHOPPING::getDef('entry_phone_dgrp') . '" data-toggle="tooltip" data-placement="right" id="frmPhoneNumB" autocomplete="tel" aria-describedby="' . CLICSHOPPING::getDef('entry_cellular_phone_number') . '" placeholder="' . CLICSHOPPING::getDef('entry_cellular_phone_number') . '"'); ?>
                </div>
              </div>
            </div>
          </div>
<?php
    }

    if (ACCOUNT_FAX == 'true') {
?>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="InputFax" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_fax_number'); ?></label>
                <div class="col-sm-6 col-md-4">
                  <?php echo HTML::inputField('fax', null, 'id="InputFax" autocomplete="tel" aria-describedby="' . CLICSHOPPING::getDef('entry_fax_number') . '" placeholder="' . CLICSHOPPING::getDef('entry_fax_number') . '"'); ?>
                </div>
              </div>
            </div>
          </div>
<?php
    }
  } // end $_GET['newcustomer']
?>
        </div>
      </div>
    </div>
      <div class="separator"></div>
      <div class="card">
        <div class="card-header">
          <span class="alert-warning float-md-right"><?php echo CLICSHOPPING::getDef('form_required'); ?></span>
          <span><h3><?php echo CLICSHOPPING::getDef('new_address_title'); ?></h3></span>
        </div>
        <div class="card-block">
          <div class="card-text">

            <div class="row">
              <div class="col-md-12">
                <div class="form-group row">
                  <label for="InputStreetAddress" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_street_address'); ?></label>
                  <div class="col-sm-6 col-md-4">
<?php
  echo HTML::inputField('street_address', ($entry['street_address'] ? $entry['street_address'] : null), 'required aria-required="true" id="InputStreetAddress" aria-describedby="' . CLICSHOPPING::getDef('entry_street_address') . '" placeholder="' . CLICSHOPPING::getDef('entry_street_address') . '" minlength="'. ENTRY_STREET_ADDRESS_PRO_MIN_LENGTH .'"');
  if ( ($CLICSHOPPING_Customer->getCustomersGroupID() == 0 && ENTRY_STREET_ADDRESS_MIN_LENGTH > 0) || ($CLICSHOPPING_Customer->getCustomersGroupID() != 0 && ENTRY_STREET_ADDRESS_PRO_MIN_LENGTH > 0 ) ) {
    echo '&nbsp;' . (!is_null(CLICSHOPPING::getDef('entry_street_address_text')) ? '<span class="text-warning">' . CLICSHOPPING::getDef('entry_street_address_text') . '</span>': '');
  }
?>
                  </div>
                </div>
              </div>
            </div>
<?php
  if (((ACCOUNT_SUBURB == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_SUBURB_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
?>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row">
                  <label for="InputSuburb" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_suburb'); ?></label>
                  <div class="col-sm-6 col-md-4">
                    <?php echo HTML::inputField('suburb', ($entry['suburb'] ? $entry['suburb'] : null), 'id="InputSuburb" aria-describedby="' . CLICSHOPPING::getDef('entry_suburb') . '" placeholder="' . CLICSHOPPING::getDef('entry_suburb') . '"'); ?>
                  </div>
                </div>
              </div>
            </div>
<?php
  }
?>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row">
                  <label for="InputPostCode" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_post_code'); ?></label>
                  <div class="col-sm-6 col-md-4">
<?php
  echo HTML::inputField('postcode', ($entry['postcode'] ? $entry['postcode'] : null), 'required aria-required="true" id="InputPostCode" aria-describedby="' . CLICSHOPPING::getDef('entry_post_code') . '" placeholder="' . CLICSHOPPING::getDef('entry_post_code') . '"');
  if ( ($CLICSHOPPING_Customer->getCustomersGroupID() == 0 && ENTRY_POSTCODE_MIN_LENGTH > 0) || ($CLICSHOPPING_Customer->getCustomersGroupID() != 0 && ENTRY_POSTCODE_PRO_MIN_LENGTH > 0 ) ) {
    echo '&nbsp;' . (!is_null(CLICSHOPPING::getDef('entry_post_code_text')) ? '<span class="text-warning">' . CLICSHOPPING::getDef('entry_post_code_text') . '</span>': '');
  }
?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="form-group row">
                  <label for="InputCity" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_city'); ?></label>
                  <div class="col-sm-6 col-md-4">
<?php
  echo HTML::inputField('city', ($entry['city'] ? $entry['city'] : null), 'required aria-required="true" id="InputCity" aria-describedby="' . CLICSHOPPING::getDef('entry_city') . '" placeholder="' . CLICSHOPPING::getDef('entry_city') . '"');
  if ( ($CLICSHOPPING_Customer->getCustomersGroupID() == 0 && ENTRY_CITY_MIN_LENGTH > 0) || ($CLICSHOPPING_Customer->getCustomersGroupID() != 0 && ENTRY_CITY_PRO_MIN_LENGTH > 0 ) ) {
    echo '&nbsp;' . (!is_null(CLICSHOPPING::getDef('entry_city_text')) ? '<span class="text-warning">' . CLICSHOPPING::getDef('entry_city_text') . '</span>': '');
  }
?>
                  </div>
                </div>
              </div>
            </div>
<?php
  if (((ACCOUNT_STATE == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((ACCOUNT_STATE_PRO == 'true') && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
    if (ACCOUNT_STATE_DROPDOWN == 'true' && !isset($_GET['Edit'])) {
?>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="InputCountry" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_country'); ?></label>
                <div class="col-sm-6 col-md-4">
                  <?php echo HTML::selectMenuCountryList('country', null, 'onchange="update_zone(this.form);" aria-required="true"'); ?>
                  <?php echo (!is_null(CLICSHOPPING::getDef('entry_country_text')) ? '<span class="text-warning">' . CLICSHOPPING::getDef('entry_country_text') . '</span>': ''); ?>
                </div>
              </div>
            </div>
          </div>

            <div class="row">
              <div class="col-md-12">
                <div class="form-group row">
                  <label for="InputState" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_state'); ?></label>
                  <div class="col-sm-6 col-md-4">
                    <?php echo HTML::selectField('state', $CLICSHOPPING_Address->getPrepareCountryZonesPullDown(), null, 'aria-required="true"'); ?>
                    <?php echo(!is_null(CLICSHOPPING::getDef('entry_state_text')) ? '<span class="text-warning">' . CLICSHOPPING::getDef('entry_state_text') . '</span>' : ''); ?>
                  </div>
                </div>
              </div>
            </div>
<?php
      include(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'ext/javascript/clicshopping/ClicShoppingAdmin/state_dropdown.php');
    } else {
?>

            <div class="row">
              <div class="col-md-12">
                <div class="form-group row">
                  <label for="InputCountry" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_country'); ?></label>
                  <div class="col-sm-6 col-md-4">
                    <?php echo HTML::selectMenuCountryList('country', (isset($entry['country_id']) ? $entry['country_id'] : STORE_COUNTRY), 'aria-required="true"'); ?>
                    <?php echo (!is_null(CLICSHOPPING::getDef('entry_country_text')) ? '<span class="text-warning">' . CLICSHOPPING::getDef('entry_country_text') . '</span>': ''); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="form-group row">
                  <label for="InputState" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_state'); ?></label>
                  <div class="col-sm-6 col-md-4">
<?php
    if ($process === true) {
      if ($entry_state_has_zones === true) {
        $zones_array = [];

        $Qcheck = $CLICSHOPPING_Db->prepare('select zone_name
                                             from :table_zones
                                             where zone_country_id = :zone_country_id
                                             and zone_status = 0
                                             order by zone_name
                                            ');
        $Qcheck->bindInt(':zone_country_id', (int)$country );
        $Qcheck->execute();

        while ($Qcheck->fetch() ) {
          $zones_array[] = ['id' => $Qcheck->value('zone_name'),
                            'text' => $Qcheck->value('zone_name')
                           ];
        }
        echo HTML::selectMenu('state', $zones_array);
      } else {
        echo HTML::inputField('state', '', 'id="inputState" placeholder="' . CLICSHOPPING::getDef('entry_state') . '"');
      }
    } else {
      echo HTML::inputField('state', (isset($entry['country_id']) ? $CLICSHOPPING_Address->getZoneName($entry['country_id'], $entry['zone_id'], $entry['entry_state']) : ''), 'id="state" placeholder="' . CLICSHOPPING::getDef('entry_state') . '"');
    }

    if (((!is_null(CLICSHOPPING::getDef('entry_state_text'))) && (ENTRY_STATE_MIN_LENGTH > 0) && ($CLICSHOPPING_Customer->getCustomersGroupID() == 0)) || ((!is_null(CLICSHOPPING::getDef('entry_state_text'))) && (ENTRY_STATE_PRO_MIN_LENGTH > 0) && ($CLICSHOPPING_Customer->getCustomersGroupID() != 0))) {
      echo '&nbsp;<span class="text-warning">' . CLICSHOPPING::getDef('entry_state_text') .'</span>';
    }
?>
                  </div>
                </div>
              </div>
            </div>

<?php
    }
  }

  if ($_GET['newcustomer'] != 1) {
//   Allow or not to customer change this address ou to change the default address if oddo is activated.
    if ((isset($_GET['edit']) && ($CLICSHOPPING_Customer->getDefaultAddressID() != $_GET['edit']) && (AddressBook::countCustomersModifyAddressDefault() == 1)) || (isset($_GET['edit']) === false) && (AddressBook::countCustomersModifyAddressDefault() == 1)) {
      if ( defined('CLICSHOPPING_APP_WEBSERVICE_ODOO_ACTIVATE_CHECKOUT_ADDRESS_CATALOG') && (CLICSHOPPING_APP_WEBSERVICE_ODOO_ACTIVATE_CHECKOUT_ADDRESS_CATALOG == 'true' || CLICSHOPPING_APP_WEBSERVICE_ODOO_ACTIVATE_WEB_SERVICE == 'false')) {
?>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row">
                  <label for="InputNewsletter" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_newsletter'); ?></label>
                  <div class="col-sm-6 col-md-4">
                    <?php echo HTML::checkboxField('primary', 'on', false, 'id="InputNewsletter" aria-label="' . CLICSHOPPING::getDef('entry_newsletter') . '"') . ' ' . CLICSHOPPING::getDef('set_as_primary');  ?>
                  </div>
                </div>
              </div>
            </div>
<?php
      }
    }
  } else {
    echo  HTML::hiddenField('primary', 'on', true, 'id="primary"');
  }
?>
          </div>
        </div>
      </div>
      <div class="separator"></div>