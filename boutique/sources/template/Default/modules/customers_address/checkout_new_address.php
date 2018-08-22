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
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_Address = Registry::get('Address');
  $CLICSHOPPING_Customer = Registry::get('Customer');

  if (($CLICSHOPPING_Customer->getCustomersGroupID() == 0 && ACCOUNT_COMPANY == 'true') || ($CLICSHOPPING_Customer->getCustomersGroupID() != 0 && ACCOUNT_COMPANY_PRO == 'true' ) ) {

    $QaccountGroup = $CLICSHOPPING_Db->prepare('select customers_company
                                         from :table_customers
                                         where customers_id = :customers_id
                                        ');
    $QaccountGroup->bindInt(':customers_id', (int)$CLICSHOPPING_Customer->getID());
    $QaccountGroup->execute();
?>
    <div class="separator"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group row">
          <label for="InputCompany" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_company'); ?></label>
          <div class="col-md-8">
<?php
  echo HTML::inputField('company', $QaccountGroup->value('customers_company'), 'id="InputCompany" aria-describedby="' . CLICSHOPPING::getDef('entry_company') . '" placeholder="' . CLICSHOPPING::getDef('entry_company') . '" minlength="'. ENTRY_COMPANY_PRO_MIN_LENGTH .'"');

  if (ENTRY_COMPANY_PRO_MIN_LENGTH > 0) {
    echo '&nbsp;' . (!is_null(CLICSHOPPING::getDef('entry_company_text_pro')) ? '<span class="text-warning">' . CLICSHOPPING::getDef('entry_company_text_pro'). '</span>': '');
  }
?>
          </div>
        </div>
      </div>
    </div>
<?php
  }

  if (ACCOUNT_GENDER == 'true') {
    $male = $female = false;

    if (isset($gender)) {
      $male = ($gender == 'm');
      $female = !$male;
    } elseif ( !$CLICSHOPPING_Customer->hasDefaultAddress() ) {
      $male = ($CLICSHOPPING_Customer->getGender() == 'm');
      $female = !$male;
    }
?>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group row">
          <label for="gender" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_gender'); ?></label>
          <div class="col-sm-6 col-md-4">
            <?php echo HTML::radioField('gender', 'm', $male) . CLICSHOPPING::getDef('male') . '&nbsp;&nbsp;' .  HTML::radioField('gender', 'f', $female) . ' ' . CLICSHOPPING::getDef('female') . '&nbsp;' . (!is_null(CLICSHOPPING::getDef('entry_gender_text')) ? '<span class="text-warning">' . CLICSHOPPING::getDef('entry_gender_text') . '</span>': ''); ?>
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
          <label for="InputFirstName" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_first_name'); ?></label>
          <div class="col-sm-6 col-md-4">
<?php
  echo HTML::inputField('firstname', (!$CLICSHOPPING_Customer->hasDefaultAddress() ? $CLICSHOPPING_Customer->getFirstName() : null), ' id="InputFirstName" aria-describedby="' . CLICSHOPPING::getDef('entry_first_name') . '" placeholder="' . CLICSHOPPING::getDef('entry_first_name') . '" minlength="'. ENTRY_FIRST_NAME_PRO_MIN_LENGTH .'"');

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
  echo HTML::inputField('lastname', (!$CLICSHOPPING_Customer->hasDefaultAddress() ? $CLICSHOPPING_Customer->getLastName() : null), ' id="InputLastName" aria-describedby="' . CLICSHOPPING::getDef('entry_last_name') . '" placeholder="' . CLICSHOPPING::getDef('entry_last_name') . '" minlength="'. ENTRY_LAST_NAME_PRO_MIN_LENGTH .'"');

  if ( ($CLICSHOPPING_Customer->getCustomersGroupID() == 0 && ENTRY_LAST_NAME_MIN_LENGTH > 0) || ($CLICSHOPPING_Customer->getCustomersGroupID() != 0 && ENTRY_LAST_NAME_PRO_MIN_LENGTH > 0 ) ) {
    echo '&nbsp;' . (!is_null(CLICSHOPPING::getDef('entry_last_name_text')) ? '<span class="text-warning">' . CLICSHOPPING::getDef('entry_last_name_text') . '</span>': '');
  }
?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group row">
          <label for="InputTelephone" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_telephone_number'); ?></label>
          <div class="col-sm-6 col-md-4">
            <?php echo HTML::inputField('telephone', null, ' id="InputTelephone" aria-describedby="' . CLICSHOPPING::getDef('entry_telephone_number') . '" placeholder="' . CLICSHOPPING::getDef('entry_telephone_number') . '"'); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group row">
          <label for="InputStreetAddress" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_street_address'); ?></label>
          <div class="col-sm-6 col-md-4">
<?php
  echo HTML::inputField('street_address', null, ' id="InputStreetAddress" aria-describedby="' . CLICSHOPPING::getDef('entry_street_address') . '" placeholder="' . CLICSHOPPING::getDef('entry_street_address') . '" minlength="'. ENTRY_STREET_ADDRESS_PRO_MIN_LENGTH .'"');
  if ( ($CLICSHOPPING_Customer->getCustomersGroupID() == 0 && ENTRY_STREET_ADDRESS_MIN_LENGTH > 0) || ($CLICSHOPPING_Customer->getCustomersGroupID() != 0 && ENTRY_STREET_ADDRESS_PRO_MIN_LENGTH > 0 ) ) {
    echo '&nbsp;' . (!is_null(CLICSHOPPING::getDef('entry_street_address_text')) ? '<span class="text-warning">' . CLICSHOPPING::getDef('entry_street_address_text') . '</span>': '');
  }
?>
          </div>
        </div>
      </div>
    </div>


<?php
  if (ACCOUNT_SUBURB == 'true') {
?>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group row">
          <label for="InputSuburb" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_suburb'); ?></label>
          <div class="col-sm-6 col-md-4">
            <?php echo HTML::inputField('street_address', null, 'id="InputSuburb" aria-describedby="' . CLICSHOPPING::getDef('entry_suburb') . '" placeholder="' . CLICSHOPPING::getDef('entry_suburb') . '"'); ?>
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
  echo HTML::inputField('postcode', null, ' id="InputPostCode" aria-describedby="' . CLICSHOPPING::getDef('entry_post_code') . '" placeholder="' . CLICSHOPPING::getDef('entry_post_code') . '"');
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
  echo HTML::inputField('city', null, ' id="InputCity" aria-describedby="' . CLICSHOPPING::getDef('entry_city') . '" placeholder="' . CLICSHOPPING::getDef('entry_city') . '"');
  if ( ($CLICSHOPPING_Customer->getCustomersGroupID() == 0 && ENTRY_CITY_MIN_LENGTH > 0) || ($CLICSHOPPING_Customer->getCustomersGroupID() != 0 && ENTRY_CITY_PRO_MIN_LENGTH > 0 ) ) {
    echo '&nbsp;' . (!is_null(CLICSHOPPING::getDef('entry_city_text')) ? '<span class="text-warning">' . CLICSHOPPING::getDef('entry_city_text') . '</span>': '');
  }
?>
            </div>
          </div>
        </div>
      </div>
<?php
  if (ACCOUNT_STATE == 'true') {
    if (ACCOUNT_STATE_DROPDOWN == 'true') {
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
        echo HTML::inputField('state', '', 'id="inputState" placeholder="' . CLICSHOPPING::getDef('entry_state') . '"');
      }

      if ( (!is_null(CLICSHOPPING::getDef('entry_state_text')) && ENTRY_STATE_MIN_LENGTH > 0 && $CLICSHOPPING_Customer->getCustomersGroupID() == 0) || (!is_null(CLICSHOPPING::getDef('entry_state_text')) && ENTRY_STATE_PRO_MIN_LENGTH > 0 && $CLICSHOPPING_Customer->getCustomersGroupID() != 0) ) {
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
?>
