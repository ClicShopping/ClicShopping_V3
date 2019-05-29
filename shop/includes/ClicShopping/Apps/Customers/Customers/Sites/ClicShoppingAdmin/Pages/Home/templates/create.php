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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin\GroupsB2BAdmin;

  $CLICSHOPPING_Customers = Registry::get('Customers');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_Address = Registry::get('Address');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');

  if ($CLICSHOPPING_MessageStack->exists('header')) {
    echo $CLICSHOPPING_MessageStack->get('header');
  }
?>
<div class="contentBody">
  <div class="separator"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <div
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/client_editer.gif', $CLICSHOPPING_Customers->getDef('heading_title'), '40', '40'); ?></div>
          <div
            class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Customers->getDef('heading_title'); ?></div>
          <div class="col-md-6 text-md-right">
            <?php
              echo HTML::form('create_account', $CLICSHOPPING_Customers->link('Customers&Create')) . HTML::hiddenField('action', 'process');
              echo HTML::button($CLICSHOPPING_Customers->getDef('button_cancel'), null, $CLICSHOPPING_Customers->link('Customers'), 'warning') . '&nbsp;';
              echo HTML::button($CLICSHOPPING_Customers->getDef('button_insert'), null, null, 'success');
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <?php
    if (isset($_GET['error']) && $error === true) {
      ?>
      <div class="alert alert-warning" role="alert">
        <?php echo $CLICSHOPPING_Customers->getDef('warning_edit_customers'); ?><br/>
      </div>
      <?php
    }
  ?>
  <!-- //################################################################################################################ -->
  <!-- //                                               FICHE CLIENT                                                      -->
  <!-- //################################################################################################################ -->
  <div class="createTabs">
    <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="myTab">
      <li
        class="nav-item"><?php echo '<a href="#tab1" role="tab" data-toggle="tab" class="nav-link active">' . $CLICSHOPPING_Customers->getDef('tab_general') . '</a>'; ?></li>
      <li
        class="nav-item"><?php echo '<a href="#tab2" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Customers->getDef('tab_societe'); ?></a></li>
      <?php
        if (MODE_B2B_B2C == 'true') {
          ?>
          <li
            class="nav-item"><?php echo '<a href="#tab3" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Customers->getDef('tab_orders'); ?></a></li>
          <?php
        }
      ?>
      <li
        class="nav-item"><?php echo '<a href="#tab4" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Customers->getDef('tab_options'); ?></a></li>
    </ul>
    <div class="tabsClicShopping">
      <div class="tab-content">
        <!-- //################################################################################################################ -->
        <!--          ONGLET NOM & ADRESSE          //-->
        <!-- //################################################################################################################ -->
        <div class="tab-pane active" id="tab1">
          <div class="col-md-12 mainTitle">
            <div class="text-md-left"><?php echo $CLICSHOPPING_Customers->getDef('category_personal'); ?></div>
          </div>
          <div class="adminformTitle">

            <div class="row" id="tab1ContentRow1">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_gender'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_gender'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::radioField('customers_gender', 'm') . '&nbsp;&nbsp;' . $CLICSHOPPING_Customers->getDef('male') . '&nbsp;&nbsp;' . HTML::radioField('customers_gender', 'f') . '&nbsp;&nbsp;' . $CLICSHOPPING_Customers->getDef('female'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab1ContentRow2">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_first_name'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_first_name'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('customers_firstname', null, 'required aria-required="true" id="firstname" placeholder="' . $CLICSHOPPING_Customers->getDef('entry_first_name') . '" minlength="' . ENTRY_FIRST_NAME_MIN_LENGTH . '"') . $CLICSHOPPING_Customers->getDef('text_field_required'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab1ContentRow3">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_last_name'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_last_name'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('customers_lastname', null, 'required aria-required="true" id="lastname" placeholder="' . $CLICSHOPPING_Customers->getDef('entry_last_name') . '" minlength="' . ENTRY_LAST_NAME_MIN_LENGTH . '"') . $CLICSHOPPING_Customers->getDef('text_field_required'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab1ContentRow4">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_date_of_birth'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_date_of_birth'); ?></label>
                  <div class="col-md-5 input-group">
                    <?php
                      if (isset($_GET['error']) && $error === true) {
                        if ($entry_date_of_birth_error === true) {
                          echo HTML::inputField('customers_dob', $cInfo->customers_dob, 'maxlength="10" style="border: 2px solid #FF0000"') . '&nbsp;' . $CLICSHOPPING_Customers->getDef('entry_date_of_birth_error');
                        } else {
                          echo $cInfo->customers_dob . HTML::hiddenField('customers_dob');
                        }
                      } else {
                        echo HTML::inputField('customers_dob', null, 'minlength="' . ENTRY_DOB_MIN_LENGTH . '"', 'date');
                      }
                    ?>
                    <span class="input-group-addon"><span class="fas fa-calendar"></span></span>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab1ContentRow5">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_email_address'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_email_address'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('customers_email_address', null, 'required aria-required="true" id="email" placeholder="' . $CLICSHOPPING_Customers->getDef('entry_email_address') . '"', 'email') . $CLICSHOPPING_Customers->getDef('text_field_required'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab1ContentRow6">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_telephone_number'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_telephone_number'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('customers_telephone', null, 'required aria-required="true" id="phone" placeholder="' . $CLICSHOPPING_Customers->getDef('entry_telephone_number') . '" minlength="' . ENTRY_TELEPHONE_MIN_LENGTH . '"', 'phone') . $CLICSHOPPING_Customers->getDef('text_field_required'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab1ContentRow7">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_cellular_phone_number'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_cellular_phone_number'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('customers_cellular_phone', null, 'id="cellular" placeholder="' . $CLICSHOPPING_Customers->getDef('entry_cellular_phone_number') . '"'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab1ContentRow8">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_fax_number'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_fax_number'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('customers_fax', null, 'id="fax" placeholder="' . $CLICSHOPPING_Customers->getDef('entry_fax_number') . '"'); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>


          <div class="separator"></div>
          <div class="col-md-12 mainTitle">
            <div class="text-md-left"><?php echo $CLICSHOPPING_Customers->getDef('category_address_default'); ?></div>
          </div>
          <div class="adminformTitle">
            <div class="row" id="tab1ContentRow9">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_street_address'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_street_address'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('customers_street_address', null, 'required aria-required="true" id="street" placeholder="' . $CLICSHOPPING_Customers->getDef('entry_street_address') . '" minlength="' . ENTRY_STREET_ADDRESS_MIN_LENGTH . '"', 'street') . $CLICSHOPPING_Customers->getDef('text_field_required') ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab1ContentRow10">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_suburb'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_suburb'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('customers_suburb', null, 'id="suburb" placeholder="' . $CLICSHOPPING_Customers->getDef('entry_suburb') . '"'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab1ContentRow11">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_country'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_country'); ?>
                    <br><?php echo $CLICSHOPPING_Customers->getDef('text_field_required'); ?>
                  </label>
                  <div class="col-md-5">
                    <?php echo HTML::selectMenuCountryList('country', null, 'onchange="update_zone(this.form);"'); ?>
                  </div>
                </div>
              </div>
            </div>
            <?php
              if (ACCOUNT_STATE == 'true') {
                ?>
                <div class="row" id="tab1ContentRow12">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_Customers->getDef('text_info_country_zone'); ?>"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('text_info_country_zone'); ?>
                      </label>
                      <div class="col-md-5">
                        <?php echo HTML::selectMenu('state', $CLICSHOPPING_Address->getPrepareCountryZonesPullDown()); ?>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
                include_once(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'ext/javascript/clicshopping/ClicShoppingAdmin/state_dropdown.php');
              }
            ?>
            <div class="row" id="tab1ContentRow13">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_post_code'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_post_code'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('postcode', null, 'required aria-required="true" id="postcode" placeholder="' . $CLICSHOPPING_Customers->getDef('entry_post_code') . '" minlength="' . ENTRY_POSTCODE_MIN_LENGTH . '"', 'postcode') . $CLICSHOPPING_Customers->getDef('text_field_required'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab1ContentRow14">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_city'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_city'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('city', null, 'required aria-required="true" id="city" placeholder="' . $CLICSHOPPING_Customers->getDef('entry_city') . '" minlength="' . ENTRY_CITY_MIN_LENGTH . '"', 'city') . $CLICSHOPPING_Customers->getDef('text_field_required'); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- -------------------------------------- //-->
        <!--          ONGLET Infos Societe          //-->
        <!-- -------------------------------------- //-->
        <div class="tab-pane" id="tab2">
          <div class="col-md-12 mainTitle" style="height:27px;" id="tab2ContentRow1">
            <div class="text-md-left"><?php echo $CLICSHOPPING_Customers->getDef('category_company'); ?></div>
            <div class="text-md-right">
              <?php
                if (MODE_B2B_B2C == 'true') {
                  ?>
                  <span
                    class="mainTitleTexteSeul"><?php echo '&nbsp;' . $CLICSHOPPING_Customers->getDef('Entry_customers_moodify_company') . '&nbsp;'; ?></span>
                  <span
                    class="mainTitleTexteSeul"><?php echo HTML::checkboxField('customers_modify_company', '1', true); ?></span>
                  <?php
                }
              ?>
            </div>
          </div>
          <div class="adminformTitle">
            <div class="row" id="tab2ContentRow2">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_company'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_company'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('customers_company', null, 'placeholder="' . $CLICSHOPPING_Customers->getDef('entry_company') . '" maxlength="32"'); ?>
                  </div>
                </div>
              </div>
            </div>
            <?php
              if (MODE_B2B_B2C == 'true') {
                ?>
                <div class="row" id="tab2ContentRow3">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_siret'); ?>"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_siret'); ?></label>
                      <div class="col-md-5">
                        <?php echo HTML::inputField('customers_siret', null, 'placeholder="' . $CLICSHOPPING_Customers->getDef('entry_siret') . '" maxlength="14"') . '&nbsp;<span class="fieldRequired">' . $CLICSHOPPING_Customers->getDef('entry_siret_exemple') . '</span>'; ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row" id="tab2ContentRow4">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_ape'); ?>"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_ape'); ?></label>
                      <div class="col-md-5">
                        <?php echo HTML::inputField('customers_ape', null, 'placeholder="' . $CLICSHOPPING_Customers->getDef('entry_ape') . '" maxlength="4"') . '&nbsp;<span class="fieldRequired">' . $CLICSHOPPING_Customers->getDef('entry_ape_exemple') . '</span>'; ?>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
                if (ACCOUNT_TVA_INTRACOM_PRO == 'true') {
                  ?>
                  <div class="row" id="tab2ContentRow5">
                    <div class="col-md-5">
                      <div class="form-group row">
                        <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_tva'); ?>"
                               class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_tva'); ?></label>
                        <div class="col-md-5">
                          <?php
                            echo HTML::selectMenuIsoList('customers_tva_intracom_code_iso', null, 'onchange="ISO_account_edit();"');
                            echo '&nbsp;' . HTML::inputField('customers_tva_intracom', null, 'placeholder="Number" maxlength="14"');
                          ?>
                        </div>
                      </div>
                    </div>
                    <span class="col-md-4">
<!-- lien pointant sur le site de verification -->
                <span>
                  </div>
                  <?php
                }
              }
            ?>
          </div>
          <?php
            // Activation du module B2B
            if (MODE_B2B_B2C == 'true') {
              ?>
              <div class="separator"></div>
              <div class="alert alert-info" id="tab2ContentRow6">
                <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Customers->getDef('title_help_customers_tva')) . ' ' . $CLICSHOPPING_Customers->getDef('title_help_customers_tva') ?></div>
                <div class="separator"></div>
                <div><?php echo $CLICSHOPPING_Customers->getDef('title_help_tva_customers'); ?></div>
              </div>
              <?php
            }
          ?>
        </div>
        <?php
          // Activation du module B2B
          if (MODE_B2B_B2C == 'true') {
            ?>
            <!-- ------------------------------------ //-->
            <!--          ONGLET Facturation          //-->
            <!-- ------------------------------------ //-->
            <div class="tab-pane" id="tab3">
              <div class="col-md-12 mainTitle" style="height:27px;" id="tab3ContentRow1">
                <div class="text-md-left"><?php echo $CLICSHOPPING_Customers->getDef('category_company'); ?></div>
              </div>
              <div class="adminformTitle">
                <div class="row" id="tab3ContentRow2">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_customers_group_name'); ?>"
                             class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_customers_group_name'); ?></label>
                      <div class="col-md-5">
                        <?php echo HTML::selectMenu('customers_group_id', GroupsB2BAdmin::getCustomersGroup($CLICSHOPPING_Customers->getDef('visitor_name'))); ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php
          }
        ?>
        <!-- ------------------------------------ //-->
        <!--          ONGLET Option          //-->
        <!-- ------------------------------------ //-->
        <div class="tab-pane" id="tab4">
          <div class="col-md-12 mainTitle" style="height:27px;">
            <div class="text-md-left"><?php echo $CLICSHOPPING_Customers->getDef('category_company'); ?></div>
          </div>
          <div class="adminformTitle">
            <div class="row" id="tab4ContentRow1">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_customers_modify_address_default'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_customers_modify_address_default'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::checkboxField('customers_modify_address_default', '1', true); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab4ContentRow2">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_customers_add_address'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_customers_add_address'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::checkboxField('customers_add_address', '1', true); ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="row" id="tab4ContentRow3">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_customers_email'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_customers_email'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::checkboxField('customers_email', '1', false); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="tab4ContentRow4">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="<?php echo $CLICSHOPPING_Customers->getDef('entry_newsletter_language'); ?>"
                         class="col-5 col-form-label"><?php echo $CLICSHOPPING_Customers->getDef('entry_newsletter_language'); ?></label>
                  <div class="col-md-5">
                    <?php
                      $languages = $CLICSHOPPING_Language->getLanguages();
                      for ($i = 0, $n = count($languages); $i < $n; $i++) {
                        $values_languages_id[$i] = array('id' => $languages[$i]['id'],
                          'text' => $languages[$i]['name']);
                      }

                      echo HTML::selectMenu('customers_languages_id', $values_languages_id);
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php echo $CLICSHOPPING_Hooks->output('DiscountCoupon', 'CreateAccount', null, 'display'); ?>


  <script type="text/javascript"><!--
      function check_form() {
          var error = 0;
          var error_message = "<?php echo $CLICSHOPPING_Customers->getDef('js_error'); ?>";

        <?php
        if (ACCOUNT_GENDER == 'true') {
        ?>
          if (document.customers.customers_gender[0].checked || document.customers.customers_gender[1].checked) {
          } else {
              error_message = error_message + <?= json_encode($CLICSHOPPING_Customers->getDef('js_gender') . "\n"); ?>;
              error = 1;
          }
        <?php
        }
        ?>

          if (customers_firstname.length < <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>) {
              error_message = error_message + <?= json_encode($CLICSHOPPING_Customers->getDef('js_first_name', ['min_length' => ENTRY_FIRST_NAME_MIN_LENGTH]) . "\n"); ?>;
              error = 1;
          }

          if (customers_lastname.length < <?php echo ENTRY_LAST_NAME_MIN_LENGTH; ?>) {
              error_message = error_message + <?= json_encode($CLICSHOPPING_Customers->getDef('js_last_name', ['min_length' => ENTRY_LAST_NAME_MIN_LENGTH]) . "\n"); ?>;
              error = 1;
          }


          if (entry_street_address.length < <?php echo ENTRY_STREET_ADDRESS_MIN_LENGTH; ?>) {
              error_message = error_message + <?= json_encode($CLICSHOPPING_Customers->getDef('js_address', ['min_length' => ENTRY_STREET_ADDRESS_MIN_LENGTH]) . "\n"); ?>;
              error = 1;
          }

          if (entry_postcode.length < <?php echo ENTRY_POSTCODE_MIN_LENGTH; ?>) {
              error_message = error_message + <?= json_encode($CLICSHOPPING_Customers->getDef('js_post_code', ['min_length' => ENTRY_POSTCODE_MIN_LENGTH]) . "\n"); ?>;
              error = 1;
          }

          if (entry_city.length < <?php echo ENTRY_CITY_MIN_LENGTH; ?>) {
              error_message = error_message + <?= json_encode($CLICSHOPPING_Customers->getDef('js_city', ['min_length' => ENTRY_CITY_MIN_LENGTH]) . "\n"); ?>;
              error = 1;
          }

        <?php
        if (ACCOUNT_STATE == 'true') {
        ?>
          if (document.customers.elements['customers_state'].type != "hidden") {
              if (document.customers.customers_state.value.length < <?php echo ENTRY_STATE_MIN_LENGTH; ?>) {
                  error_message = error_message + <?= json_encode($CLICSHOPPING_Customers->getDef('js_state') . "\n"); ?>;
                  error = 1;
              }
          }
        <?php
        }
        ?>

          if (document.customers.elements['entry_country_id'].type != "hidden") {
              if (document.customers.entry_country_id.value == 0) {
                  error_message = error_message + <?= json_encode($CLICSHOPPING_Customers->getDef('js_country') . "\n"); ?>;
                  error = 1;
              }
          }

          if (customers_telephone.length < <?php echo ENTRY_TELEPHONE_MIN_LENGTH; ?>) {
              error_message = error_message + <?= json_encode($CLICSHOPPING_Customers->getDef('js_telephone', ['min_length' => ENTRY_TELEPHONE_MIN_LENGTH]) . "\n"); ?>;
              error = 1;
          }

          if (error == 1) {
              alert(error_message);
              return false;
          } else {
              return true;
          }
      }

      //--></script>
  </form>
</div>