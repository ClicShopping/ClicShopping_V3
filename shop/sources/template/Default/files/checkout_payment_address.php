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
  use ClicShopping\OM\Registry;

  use ClicShopping\Sites\Shop\AddressBook;

  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_Template = Registry::get('Template');
  $CLICSHOPPING_Customer = Registry::get('Customer');
  $CLICSHOPPING_Address = Registry::get('Address');

  if ( $CLICSHOPPING_MessageStack->exists('checkout_address') ) {
    echo $CLICSHOPPING_MessageStack->get('checkout_address');
  }

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

  echo HTML::form('checkout_address', CLICSHOPPING::link(null, 'Checkout&PaymentAddress&Process'), 'post', 'role="form" id="usrForm"', ['tokenize' => true, 'action' => 'process']);

// ------------------------------
// --- Payment address   -----
// -------------------------------
?>
<section class="checkout_address" id="checkout_address">
  <div class="contentContainer">
    <div class="contentText">
<?php
  if ( $CLICSHOPPING_Customer->hasDefaultAddress() ) {
?>
     <div class="separator"></div>
     <h3><?php echo CLICSHOPPING::getDef('table_heading_payment_address'); ?></h3>

      <span class="col-md-6 float-md-left">
        <?php echo CLICSHOPPING::getDef('text_selected_payment_destination'); ?>
      </span>

      <span class="col-md-6 float-md-right">
        <div class="card card-default">
          <div class="card-header"><?php echo CLICSHOPPING::getDef('title_payment_address'); ?></div>
          <div class="card-block">
            <div class="separator"></div>
            <?php echo AddressBook::addressLabel($CLICSHOPPING_Customer->getID(), $_SESSION['billto'], true, ' ', '<br />'); ?>
          </div>
        </div>
      </span>
      <div class="clearfix"></div>
<?php
      $addresses_count = AddressBook::countCustomerAddressBookEntries();

      if ($addresses_count > 1) {
?>
      <h3><?php echo CLICSHOPPING::getDef('table_heading_address_book_entries'); ?></h3>
<?php
// -----------------------------------
// --- Select other destination   -----
// ------------------------------- ----
?>
     <div><?php echo CLICSHOPPING::getDef('text_select_other_payment_destination'); ?></div>
     <div class="separator"></div>
     <div class="d-flex flex-wrap ">
<?php
        $radio_buttons = 0;
        $Qaddresses = AddressBook::getListing();

        while ($addresses = $Qaddresses->fetch()) {
          $format_id = $CLICSHOPPING_Address->getAddressFormatId($Qaddresses->valueInt('country_id'));
?>
        <div class="col-md-4">
          <div class="card panel-<?php echo ($Qaddresses->valueInt('address_book_id') == $CLICSHOPPING_Customer->hasDefaultAddress()) ? 'primary' : 'default'; ?>">
            <div class="card-header"><strong><?php echo HTML::outputProtected($Qaddresses->value('firstname') . ' ' . $Qaddresses->value('lastname')); ?></strong></div>
            <div class="card-block">
              <div class="separator"></div>
              <?php echo $CLICSHOPPING_Address->addressFormat($format_id, $addresses, true, ' ', '<br />'); ?>
            </div>
            <div class="card-footer text-md-center">
              <div class="custom-control custom-radio custom-control-inline">
                <?php echo HTML::radioField('address', $Qaddresses->valueInt('address_book_id'), ($Qaddresses->valueInt('address_book_id') == $_SESSION['billto']), 'class="custom-control-input" id="address_book_id' . $radio_buttons .'" name="address_book_id' . $radio_buttons .'"'); ?>
                <label class="custom-control-label" for="address_book_id<?php echo $radio_buttons; ?>"</label>
              </div>
            </div>
          </div>
        </div>
<?php
          $radio_buttons++;
        } // end while
?>
      </div>
<?php
      } // $addresses_count
    } else {
      $process = true;
    } // has_default

// ------------------------------
// --- new address -------------
// -------------------------------
  if ($addresses_count < MAX_ADDRESS_BOOK_ENTRIES) {
?>

    <div class="separator"></div>
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <span class="alert-warning float-md-right" role="alert"><?php echo CLICSHOPPING::getDef('form_required'); ?></span>
          <h3><span><?php echo CLICSHOPPING::getDef('table_heading_new_payment_address'); ?></span></h3>
        </div>

        <div class="card-block">
          <div class="separator"></div>
          <div class="card-text">
            <span><?php echo CLICSHOPPING::getDef('text_create_new_payment_address'); ?></span>
<?php
    if ($CLICSHOPPING_Customer->getCustomersGroupID() == 0 || ACCOUNT_ADRESS_BOOK_PRO == 'true') {
      require_once($CLICSHOPPING_Template->getTemplateModules('customers_address/checkout_new_address'));
    }
?>
          </div>
        </div>
      </div>
    </div>
<?php
  }
?>
    <div class="separator"></div>
    <div class="control-group">
     <div class="controls">
       <div class="buttonSet">
         <span class="float-md-right"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, null, 'success'); ?></span>
       </div>
     </div>
    </div>
<?php
  if ($process === true) {
?>
    <div class="control-group">
      <div class="controls">
        <div class="buttonSet">
          <span class="float-md-left"> <?php echo  HTML::button(CLICSHOPPING::getDef('button_back'), '', CLICSHOPPING::link('checkout_payment_address.php'),'info'); ?></span>
        </div>
      </div>
    </div>
<?php
  }
?>
    </div>
    <div class="separator"></div>
  </div>
  <div class="separator"></div>
</section>
</form>