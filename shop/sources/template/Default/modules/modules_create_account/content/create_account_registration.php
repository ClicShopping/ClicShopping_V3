<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  echo $form
?>
  <div class="col-md-<?php echo $content_width; ?>">
<?php
  if ( $CLICSHOPPING_MessageStack->exists('create_account') ) {
?>
    <div class="alert-warning" role="alert"><?php echo $CLICSHOPPING_MessageStack->get('create_account'); ?></div>
    <div class="separator"></div>
<?php
  }
?>
    <div class="card">
      <div class="card-header">
        <span class="alert-warning float-md-right" role="alert"><?php echo CLICSHOPPING::getDef('form_required'); ?></span>
        <span class="modulesCreateAccountRegistrationPageHeader"><h3><?php echo CLICSHOPPING::getDef('category_personal'); ?></h3></span>
      </div>
      <div class="card-block">
        <div class="separator"></div>
        <div class="card-text">
          <div class="row" id="RowContentAccountRegistration1">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="InputFirstName" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_first_name'); ?></label>
                <div class="col-sm-6 col-md-6">
                  <?php echo HTML::inputField('firstname', null, 'required aria-required="true" id="InputFirstName" autocomplete="name" aria-describedby="' . CLICSHOPPING::getDef('entry_first_name') . '" placeholder="' . CLICSHOPPING::getDef('entry_first_name') . '" minlength="'. ENTRY_FIRST_NAME_MIN_LENGTH .'"'); ?>
                </div>
              </div>
            </div>
          </div>

          <div class="row" id="RowContentAccountRegistration2">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="InputLastName" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_last_name'); ?></label>
                <div class="col-sm-6 col-md-6">
                  <?php echo HTML::inputField('lastname', null, 'required aria-required="true" id="InputLastName" autocomplete="name" aria-describedby="' . CLICSHOPPING::getDef('entry_last_name') . '" placeholder="' . CLICSHOPPING::getDef('entry_last_name') . '" minlength="'. ENTRY_LAST_NAME_MIN_LENGTH .'"'); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="separator"></div>
<?php
  if (ACCOUNT_DOB == 'true') {
?>
          <div class="separator"></div>

          <div class="row" id="RowContentAccountRegistration3">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="dob" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_date_of_birth'); ?></label>
                <div class="col-sm-6 col-md-6">
                  <?php echo HTML::inputField('dob', null, 'rel="txtTooltipDob" title="' . CLICSHOPPING::getDef('text_create_account_dob_dgrp') . '" data-toggle="tooltip" data-placement="right" required aria-required="true" aria-describedby="' . CLICSHOPPING::getDef('entry_date_of_birth') . '" placeholder="' . CLICSHOPPING::getDef('entry_date_of_birth') . '" minlength="'. ENTRY_DOB_MIN_LENGTH .'"', 'date'); ?>
                </div>
              </div>
            </div>
          </div>
<?php
  }
?>
          <div class="row" id="RowContentAccountRegistration4">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="InputEmail" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_email_address'); ?></label>
                <div class="col-sm-6 col-md-6">
                  <?php echo HTML::inputField('email_address', null, 'rel="txtTooltipEmailAddress" autocomplete="email" title="' . CLICSHOPPING::getDef('entry_email_address') . '" data-toggle="tooltip" data-placement="right" required aria-required="true" id="InputEmail" aria-describedby="' . CLICSHOPPING::getDef('entry_email_address') . '" placeholder="' . CLICSHOPPING::getDef('entry_email_address') . '"', 'email'); ?>
                </div>
              </div>
            </div>
          </div>

          <div class="row" id="RowContentAccountRegistration5">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="InputEmailConfirm" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_email_address_confirmation'); ?></label>
                <div class="col-sm-6 col-md-6">
                  <?php echo HTML::inputField('email_address_confirm', null, 'required aria-required="true" id="InputEmailConfirm" autocomplete="email" aria-describedby="' . CLICSHOPPING::getDef('entry_email_address_confirmation') . '" placeholder="' . CLICSHOPPING::getDef('entry_email_address_confirmation') . '"', 'email'); ?>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="separator"></div>
<?php
// ----------------------
// Newsletter Information
// ----------------------
?>
    <div class="card">
      <div class="card-header">
        <span class="modulesCreateAccountRegistrationCategoryOptionsPageHeader"><h3><?php echo CLICSHOPPING::getDef('entry_newsletter'); ?></h3></span>
      </div>
      <div class="card-block">
        <div class="separator"></div>
        <div class="card-text">
          <div class="row" id="RowContentAccountRegistration6">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="Inputnewsletter" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_newsletter'); ?></label>
                <div class="col-sm-6 col-md-6">
                  <?php echo HTML::checkboxField('newsletter', 1, false, 'id="Inputnewsletter" aria-label="' . CLICSHOPPING::getDef('entry_newsletter') . '"'); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
<?php
  // ----------------------
  // Password
  // ----------------------
?>
    <div class="card">
      <div class="card-header">
        <span class="alert-warning float-md-right" role="alert"><?php echo CLICSHOPPING::getDef('form_required'); ?></span>
        <span class="modulesCreateAccountRegistrationPasswordPageHeader"><h3><?php echo CLICSHOPPING::getDef('category_password'); ?></h3></span>
      </div>
      <div class="card-block">
        <div class="separator"></div>
        <div class="card-text">
          <div class="row" id="RowContentAccountRegistration7">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="inputPassword" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_password'); ?></label>
                <div class="col-sm-6 col-md-6">
                  <?php echo HTML::inputField('password', null, 'required aria-required="true" id="inputPassword" aria-describedby="' . CLICSHOPPING::getDef('entry_password') . '" placeholder="' . CLICSHOPPING::getDef('entry_password') . '" minlength="'. ENTRY_PASSWORD_MIN_LENGTH .'"', 'password'); ?>
                </div>
              </div>
            </div>
          </div>

          <div class="row" id="RowContentAccountRegistration8">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="inputPasswordconfirmation" class="col-sm-6 col-md-4 col-form-label"><?php echo CLICSHOPPING::getDef('entry_password_confirmation'); ?></label>
                <div class="col-sm-6 col-md-6">
                  <?php echo HTML::inputField('confirmation', null, 'required aria-required="true" id="inputPasswordconfirmation" aria-describedby="' . CLICSHOPPING::getDef('entry_password_confirmation') . '" placeholder="' . CLICSHOPPING::getDef('entry_password_confirmation') . '" minlength="'. ENTRY_PASSWORD_MIN_LENGTH .'"', 'password'); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
  </div>


