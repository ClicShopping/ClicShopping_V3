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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Mail;

  class co_contact_us_form {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('modules_contact_us_form_title');
      $this->description = CLICSHOPPING::getDef('modules_contact_us_form_description');

      if (defined('MODULES_CONTACT_US_FORM_STATUS')) {
        $this->sort_order = (int)MODULES_CONTACT_US_FORM_SORT_ORDER;
        $this->enabled = (MODULES_CONTACT_US_FORM_STATUS == 'True');
      }
    }

    public function execute() {
      global $send_to_array;

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (isset($_GET['Info']) && isset($_GET['Contact']) && !isset($_GET['Success'])) {

        $content_width = (int)MODULE_CONTACT_US_FORM_CONTENT_WIDTH;

        $form = HTML::form('contact', CLICSHOPPING::link('index.php', 'Info&Contact&Process&action=process'), 'post', 'enctype="multipart/form-data"',  ['tokenize' => true]);
        $endform ='</form>';

        if ( isset($_GET['order_id']) && is_numeric($_GET['order_id']) ) {
          $order_id = HTML::sanitize($_GET['order_id']);
        }

        $number_confirmation = Mail::getConfirmationNumberAntiSpam();

        $contact_us_form = '<!--  contact_us_form start -->' . "\n";
        $contact_us_form .= '<div class="col-md-' . $content_width . '">';
        $contact_us_form .= $form;

        if ($order_id == 0 && !$CLICSHOPPING_Customer->isLoggedOn()) {
          $contact_us_form .= '<div class="separator"></div>';
          $contact_us_form .= '<div class="col-md-12">'. CLICSHOPPING::getDef('entry_note_no_registered') . '</div>';
          $contact_us_form .= '<div class="separator"></div>';
        }

        if ($CLICSHOPPING_Customer->isLoggedOn()) {
          $contact_us_form .= '
            <div class="row">
              <div class="col-md-7">
                <div class="form-group row">
                  <label for="dob" class="col-6 col-form-label">'. CLICSHOPPING::getDef('entry_name') . '</label>
                  <div class="col-md-6">
                      ' . $CLICSHOPPING_Customer->getLastName() . '  ' . $CLICSHOPPING_Customer->getFirstName() . HTML::hiddenField('name', $CLICSHOPPING_Customer->getLastName() . ' ' . $CLICSHOPPING_Customer->getFirstName()) . '
                  </div>
                </div>
              </div>
            </div>
          ';

        } else {
          $contact_us_form .= '
            <div class="row">
              <div class="col-md-7">
                <div class="form-group row">
                  <label for="dob" class="col-6 col-form-label">'. CLICSHOPPING::getDef('entry_name') . '</label>
                  <div class="col-md-6">
                      ' . HTML::inputField('name', null, 'required aria-required="true" id="InputName" aria-describedby="' . CLICSHOPPING::getDef('entry_name') . '" placeholder="' . CLICSHOPPING::getDef('entry_name') . '"') . '
                  </div>
                </div>
              </div>
            </div>
          ';
        }
        $contact_us_form .= '<div class="separator"></div>';

        if ($CLICSHOPPING_Customer->isLoggedOn()) {
          $contact_us_form .= '
            <div class="row">
              <div class="col-md-7">
                <div class="form-group row">
                  <label for="dob" class="col-6 col-form-label">'. CLICSHOPPING::getDef('entry_email') . '</label>
                  <div class="col-md-6">
                      ' . $CLICSHOPPING_Customer->getEmailAddress(). HTML::hiddenField('email', $CLICSHOPPING_Customer->getEmailAddress()) . '
                  </div>
                </div>
              </div>
            </div>
          ';
        } else {
          $contact_us_form .= '
            <div class="row">
              <div class="col-md-7">
                <div class="form-group row">
                  <label for="dob" class="col-6 col-form-label">'. CLICSHOPPING::getDef('entry_email') . '</label>
                  <div class="col-md-6">
                      ' . HTML::inputField('email', null, 'rel="txtTooltipEmailAddress" title="' . CLICSHOPPING::getDef('entry_email_dgrp') . '" data-toggle="tooltip" data-placement="right" required aria-required="true" id="InputEmail" aria-describedby="' . CLICSHOPPING::getDef('entry_email') . '" placeholder="' . CLICSHOPPING::getDef('entry_email') . '"', 'email') . '
                  </div>
                </div>
              </div>
            </div>
          ';
        }

        $contact_us_form .= '<div class="separator"></div>';

        if ($CLICSHOPPING_Customer->isLoggedOn()) {

          $contact_us_form .= '
              <div class="row">
                <div class="col-md-7">
                  <div class="form-group row">
                    <label for="inputTelephone" class="col-6 col-form-label">' . CLICSHOPPING::getDef('entry_customers_phone') . '</label>
                    <div class="col-md-6">
                      ' . $CLICSHOPPING_Customer->getTelephone() . HTML::hiddenField('customers_telephone', $CLICSHOPPING_Customer->getTelephone() ) . '
                    </div>
                  </div>
                </div>
              </div>
            ';

        } else {

          $contact_us_form .= '
              <div class="row">
                <div class="col-md-7">
                  <div class="form-group row">
                    <label for="inputTelephone" class="col-6 col-form-label">' . CLICSHOPPING::getDef('entry_customers_phone') . '</label>
                    <div class="col-md-6">
                      ' . HTML::inputField('customers_telephone', null, 'rel="txtTooltipPhone" title="' . CLICSHOPPING::getDef('entry_customers_phone_dgrp') . '" data-toggle="tooltip" data-placement="right" required aria-required="true" id="InputTelephone" aria-describedby="' . CLICSHOPPING::getDef('entry_customers_phone') . '" placeholder="' . CLICSHOPPING::getDef('entry_customers_phone') . '"', 'phone') . '
                    </div>
                  </div>
                </div>
              </div>
            ';
        }


// ----------------------
// Support
// ----------------------

        if ($CLICSHOPPING_Customer->isLoggedOn()) {
          $contact_us_form .= '
              <div class="row">
                <div class="col-md-7">
                  <div class="form-group row">
                    <label for="entry_customers_id" class="col-6 col-form-label">' . CLICSHOPPING::getDef('entry_customers_id') . '</label>
                    <div class="col-md-6">
                       ' . HTML::hiddenField('customer_id', $CLICSHOPPING_Customer->getID()) . (int)$CLICSHOPPING_Customer->getID() . '
                    </div>
                  </div>
                </div>
              </div>
            ';
// customer registered with order number
          if ($order_id == 0) {
            $contact_us_form .= '<div>';
            $contact_us_form .= '<label for="entry_note_registred" class="col-md-12">'. CLICSHOPPING::getDef('entry_note_registered') . '</label>';
            $contact_us_form .= '</div>';

          } else {
// customer registered with no order number
            $contact_us_form .= '
              <div class="row">
                <div class="col-md-7">
                  <div class="form-group row">
                    <label for="gender" class="col-6 col-form-label">' . CLICSHOPPING::getDef('entry_order') . '</label>
                    <div class="col-md-6">
                      '. $order_id . ' ' . HTML::hiddenField('order_id', (int)$order_id) . '
                    </div>
                  </div>
                </div>
              </div>
            ';

            $contact_us_form .= '
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group row">
                    <div class="col-md-12 alert alert-warning">
                      <h6>' . CLICSHOPPING::getDef('entry_note_registered')  . '</h6>
                    </div>
                  </div>
                </div>
              </div>
            ';

          }
        }

        if (!empty(CONTACT_DEPARTMENT_LIST)) {
          $contact_us_form .= '
            <div class="row">
              <div class="col-md-7">
                <div class="form-group row">
                  <label for="CompanyDepartment" class="col-6 col-form-label">'. CLICSHOPPING::getDef('send_department_company') . '</label>
                  <div class="col-md-6">
                    ' . HTML::selectMenu('send_to', $send_to_array, null, null, false, 'inputContacUsPullDownMenu') . '
                  </div>
                </div>
              </div>
            </div>
          ';
        } else {
          define('CONTACT_DEPARTMENT_LIST', 'Not configured');
        }
// ----------------------
// Subject
// -------
        $contact_us_form .= '
            <div class="row">
              <div class="col-md-7">
                <div class="form-group row">
                  <label for="Email" class="col-6 col-form-label">'. CLICSHOPPING::getDef('entry_customers_subject') . '</label>
                  <div class="col-md-6">
                      ' . HTML::inputField('email_subject', null, 'required aria-required="true" id="Inputsubject" aria-describedby="' . CLICSHOPPING::getDef('entry_customers_subject') . '" placeholder="' . CLICSHOPPING::getDef('entry_customers_subject') . '"') . '
                  </div>
                </div>
              </div>
            </div>
          ';

// ----------------------
// Enquiry
// ----------------------

        $contact_us_form .= '<div class="separator"></div>';
        $contact_us_form .= '
            <div class="row">
              <div class="col-md-7">
                <div class="form-group row">
                  <label for="inputMessage" class="col-4 col-form-label">' . CLICSHOPPING::getDef('entry_enquiry') . '</label>
                  </div>
              </div>
              <div class="col-md-10">
                ' . HTML::textAreaField('enquiry', null, 50, 15, 'class="form-control" required aria-required="true" id="inputMessage" placeholder="' . CLICSHOPPING::getDef('entry_enquiry') . '" class="inputContacUsFormTextArea"') . '
              </div>
             </div>
            ';

// ----------------------
// Evidence files
// ----------------------
        if ($order_id > 0 && $CLICSHOPPING_Customer->isLoggedOn() && MODULE_CONTACT_US_FORM_EVIDENCE == 'True' ) {
          $contact_us_form .= '<div class="separator"></div>';
          $contact_us_form .= '
            <div class="row">
              <div class="col-md-7">
                <div class="form-group row">
                  <label for="evidenceDocument" class="col-4 col-form-label">'. CLICSHOPPING::getDef('entry_enquiry') . '</label>
                  <div class="col-md-8">
                    ' . HTML::fileField('evidence_document', 'id="file"') . '
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-7">
                <span class="col-md-12 alert alert-warning" role="alert">' . CLICSHOPPING::getDef('text_becarefull_download') . '  '. (int)(ini_get('upload_max_filesize')) . CLICSHOPPING::getDef('text_becarefull_download_1') . '</span>
              </div>
            </div>
          ';
        }

// ----------------------
// Confirmation number
// ----------------------
        $contact_us_form .= '<div class="separator"></div>';
        $contact_us_form .= '
           <div class="row">
              <div class="col-md-8">
                <div class="form-group row">
                  <label for="inputVerificationCode" class="col-4 col-form-label">'. CLICSHOPPING::getDef('entry_number_email_confirmation') . '<span class="text-warning">' . HTML::outputProtected($number_confirmation) . '</span></label>
                  <div class="col-md-5">
                    ' .  HTML::inputField('number_email_confirmation', null, 'required aria-required="true" id="inputVerificationCode" aria-describedby="' . CLICSHOPPING::getDef('entry_number_email_confirmation') . '" placeholder="' . CLICSHOPPING::getDef('entry_number_email_confirmation') . '"') . '
                  </div>
                </div>
              </div>
            </div>
       ';

  if (DISPLAY_PRIVACY_CONDITIONS == 'true') {
        $contact_us_form .= '
              <div class="separator"></div>
              <div class="col-md-12">
                <div class="separator"></div>
                <div class="modulesContactUsTextPrivacy">
                  ' . HTML::checkboxField('customer_agree_privacy', null, 'required aria-required="true"') . ' ' . CLICSHOPPING::getDef('text_privacy_conditions_agree') . '<br />
                  ' . CLICSHOPPING::getDef('text_privacy_conditions_description', ['store_name' => STORE_NAME, 'privacy_url' => CLICSHOPPING::link(SHOP_CODE_URL_CONFIDENTIALITY)]) . '
                </div>
              </div>
        ';
  }


// ----------------------
// Confirmation Recaptcha
// ----------------------
        if (defined('MODULES_HEADER_TAGS_GOOGLE_RECAPTCHA_CREATE_ACCOUNT')) {
          if (MODULES_HEADER_TAGS_GOOGLE_RECAPTCHA_CREATE_ACCOUNT == 'True') {
            $contact_us_form .= '<div class="separator"></div>';
            $contact_us_form .= '
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group row">
            ';
            $contact_us_form .= $CLICSHOPPING_Hooks->output('AllShop', 'GoogleRecaptchaDisplay');
            $contact_us_form .= '
                </div>
              </div>
            </div>
            ';
          }
        }

// ----------------------
// Confirmation Button
// ----------------------
        $contact_us_form .= '<div class="separator"></div>';
        $contact_us_form .= '<div class="control-group">';
        $contact_us_form .= '<div class="buttonSet">';
        $contact_us_form .= '<div class="text-md-right" style="padding-right:10px;">';
        $contact_us_form .= HTML::button(CLICSHOPPING::getDef('button_continue'), null, null, 'primary', null, null, null, '"submit"');
        $contact_us_form .= '</div>';
        $contact_us_form .= '</div>';
        $contact_us_form .= '</div>';

        $contact_us_form .= $endform;

        $contact_us_form .= '</div>' . "\n";

        $contact_us_form .= '<!-- contact_us_form end -->' . "\n";

        $CLICSHOPPING_Template->addBlock($contact_us_form, $this->group);
      } // end
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULES_CONTACT_US_FORM_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULES_CONTACT_US_FORM_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want activate this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the module',
          'configuration_key' => 'MODULE_CONTACT_US_FORM_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Select a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_text_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want display evidence system ?',
          'configuration_key' => 'MODULE_CONTACT_US_FORM_EVIDENCE',
          'configuration_value' => 'False',
          'configuration_description' => 'Display a field allowing to upload evidence mp4, jpg, pdf',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULES_CONTACT_US_FORM_SORT_ORDER',
          'configuration_value' => '100',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '20',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                               ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
      );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array('MODULES_CONTACT_US_FORM_STATUS',
                   'MODULE_CONTACT_US_FORM_CONTENT_WIDTH',
                   'MODULE_CONTACT_US_FORM_EVIDENCE',
                   'MODULES_CONTACT_US_FORM_SORT_ORDER'
                  );
    }
  }
