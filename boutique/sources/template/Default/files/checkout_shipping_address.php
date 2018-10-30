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

  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_Template = Registry::get('Template');
  $CLICSHOPPING_Customer = Registry::get('Customer');
  $CLICSHOPPING_Address = Registry::get('Address');

  if ( $CLICSHOPPING_MessageStack->exists('checkout_address') ) {
    echo $CLICSHOPPING_MessageStack->get('checkout_address');
  }

  require($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

  echo HTML::form('checkout_shipping_address', CLICSHOPPING::link('index.php', 'Checkout&ShippingAddress&Process'), 'post', 'role="form" id="usrForm"', ['tokenize' => true, 'action' => 'process']);

// ------------------------------
// --- Shipping address   -----
// ------------------------------
?>
<section class="checkout_shipping_address" id="checkout_shipping_address">
  <div class="contentContainer">
    <div class="contentText">
<?php
  if ( $CLICSHOPPING_Customer->hasDefaultAddress() ) {
    if ($process === false) {
?>
     <div class="separator"></div>
     <h3><?php echo CLICSHOPPING::getDef('table_heading_shipping_address'); ?></h3>

      <span class="col-md-6 float-md-left">
         <div><?php echo CLICSHOPPING::getDef('text_selected_shipping_destination'); ?></div>
      </span>

      <span class="col-md-6 float-md-right">
        <div class="card card-default">
          <div class="card-header"><?php echo CLICSHOPPING::getDef('title_shipping_address'); ?></div>
          <div class="card-block">
            <?php echo AddressBook::addressLabel($CLICSHOPPING_Customer->getID(), $_SESSION['sendto'], true, ' ', '<br />'); ?>
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
      <div><?php echo CLICSHOPPING::getDef('text_select_other_shipping_destination'); ?></div>
      <div class="separator"></div>
      <div class="d-flex flex-wrap ">
<?php
        $radio_buttons = 0;

        $Qaddresses = AddressBook::getListing();

        while ($addresses = $Qaddresses->fetch() ) {
          $format_id = $CLICSHOPPING_Address->getAddressFormatId($Qaddresses->valueInt('country_id'));
?>
        <div class="col-md-4">
          <div class="card panel-<?php echo ($Qaddresses->valueInt('address_book_id') == $CLICSHOPPING_Customer->hasDefaultAddress()) ? 'primary' : 'default'; ?>">
            <div class="card-header"><strong><?php echo HTML::outputProtected($Qaddresses->value('firstname') . ' ' . $Qaddresses->value('lastname')); ?></strong></div>
            <div class="card-block"><?php echo $CLICSHOPPING_Address->addressFormat($format_id, $addresses, true, ' ', '<br />'); ?> </div>
            <div class="card-footer text-md-center"><?php echo HTML::radioField('address', $Qaddresses->valueInt('address_book_id'), ($Qaddresses->valueInt('address_book_id')== $_SESSION['sendto'])); ?></div>
          </div>
        </div>
<?php
          $radio_buttons++;
        } // end while
?>
      </div>
<?php
      } // $addresses_count
    } // $process
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
          <span class="alert-warning float-md-right"><?php echo CLICSHOPPING::getDef('form_required'); ?></span>
          <span><h3><?php echo CLICSHOPPING::getDef('table_heading_new_shipping_address'); ?></h3></span>
        </div>

        <div class="card-block">
          <div class="card-text">
          <span><?php echo CLICSHOPPING::getDef('text_create_new_shipping_address'); ?></span>
<?php
    if ($CLICSHOPPING_Customer->getCustomersGroupID() == 0 || ACCOUNT_ADRESS_BOOK_PRO == 'true') {
      require($CLICSHOPPING_Template->getTemplateModules('customers_address/checkout_new_address'));
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
          <span class="float-md-left"><?php echo HTML::button(CLICSHOPPING::getDef('button_back'), '', CLICSHOPPING::link('index.php', 'Checkout&ShippingAddress'),'info'); ?></span>
        </div>
      </div>
    </div>
<?php
  }
?>
    </div>
  </div>
  <div class="separator"></div>
</section>
</form>