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

  $CLICSHOPPING_Address = Registry::get('Address');

  if ( $CLICSHOPPING_MessageStack->exists('addressbook') ) {
    echo $CLICSHOPPING_MessageStack->get('addressbook');
  }

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

// error checking when updating or adding an entry
  $process = false;

// ----------------------
// --- Primary Address---
// ----------------------
?>
<section class="address_book" id="address_book">
  <div class="contentContainer">
    <div class="contentText">
      <div class="separator"></div>
      <h3><?php echo CLICSHOPPING::getDef('primary_address_title'); ?></h3>
        <div class="col-md-9">
          <div><?php echo CLICSHOPPING::getDef('primary_address_description', ['store_name' => STORE_NAME]); ?></div>
        </div>
        <div class="separator"></div>
        <div class="col-md-3">
          <div class="boxHeading"><strong><?php echo CLICSHOPPING::getDef('primary_address_title'); ?></strong></div>
          <div class="boxContents">
            <?php echo AddressBook::addressLabel($CLICSHOPPING_Customer->getID(), $CLICSHOPPING_Customer->getDefaultAddressID(), true, ' ', '<br />'); ?>
          </div>
        </div>
<?php
// ----------------------
// --- Address bool Title
// ----------------------
?>
      <div class="separator"></div>
    <div class="separator"></div>
    <h3><?php echo CLICSHOPPING::getDef('primary_book_title'); ?></h3></>
    <div class="d-flex flex-wrap">
<?php
  $Qaddresses = AddressBook::getListing();

  while ($addresses = $Qaddresses->fetch()) {
    $format_id = $CLICSHOPPING_Address->getAddressFormatId($Qaddresses->valueInt('country_id'));
?>

      <div class="col-md-6">
        <div class="card panel-<?php echo ($Qaddresses->valueInt('address_book_id') == $CLICSHOPPING_Customer->getDefaultAddressID()) ? 'primary' : 'default'; ?>">
<?php
  // Controle autorisation au client de modifier son adresse par defaut
  if ((AddressBook::countCustomersModifyAddressDefault() == 0) && ($Qaddresses->valueInt('address_book_id') == $CLICSHOPPING_Customer->getDefaultAddressID())) {
?>
          <div class="card-header">
            <strong><?php echo HTML::outputProtected($Qaddresses->value('firstname') . ' ' . $Qaddresses->value('lastname')); ?></strong><?php if ($Qaddresses->valueInt('address_book_id') == $CLICSHOPPING_Customer->getDefaultAddressID()) echo '&nbsp;<small><i>' . CLICSHOPPING::getDef('primary_address') . '</i></small>'; ?>
          </div>
          <div class="card-block">
            <div class="separator"></div>
            <?php echo $CLICSHOPPING_Address->addressFormat($format_id, $addresses, true, ' ', '<br />'); ?>
          </div>
<?php
  // Autorisation de modifier l'adresse par defaut du client
  } else {
?>
          <div class="card-header">
            <strong><?php echo HTML::outputProtected($Qaddresses->value('firstname') . ' ' . $Qaddresses->value('lastname')); ?></strong><?php if ($Qaddresses->valueInt('address_book_id') == $CLICSHOPPING_Customer->getDefaultAddressID()) echo '&nbsp;<small><i>' . CLICSHOPPING::getDef('primary_address') . '</i></small>'; ?>
          </div>
          <div class="card-block">
            <div class="separator"></div>
            <?php echo $CLICSHOPPING_Address->addressFormat($format_id, $addresses, true, ' ', '<br />'); ?>
          </div>
          <div class="card-footer text-md-center">
            <?php echo HTML::button(CLICSHOPPING::getDef('button_edit'), null, CLICSHOPPING::link(null, 'Account&AddressBookProcess&Edit&edit=' . $Qaddresses->valueInt('address_book_id')),'success', null, 'sm') .  ' ' . HTML::button(CLICSHOPPING::getDef('button_delete'), null, CLICSHOPPING::link(null, 'Account&AddressBookProcess&Delete&delete=' . $Qaddresses->valueInt('address_book_id')),  'danger', null, 'sm'); ?>
          </div>
<?php
  }
?>
        </div>
      </div>
<?php
  }
?>
    </div>
<?php
  // ----------------------
  // --- Max Address   -----
  // ----------------------
  if (AddressBook::countCustomersAddAddress() == 1) {
?>
      <div class="separator"></div>
      <div>
        <div><?php echo CLICSHOPPING::getDef('text_maximum_entries', ['max_entries' => (int)MAX_ADDRESS_BOOK_ENTRIES]); ?></div>
      </div>
<?php
  }
// ----------------------
// --- Button   -----
// ----------------------
?>
        <div class="control-group">
        <div class="controls">
          <span class="buttonSet">
            <span class="col-md-6"><?php echo HTML::button(CLICSHOPPING::getDef('button_back'), null, CLICSHOPPING::link(null, 'Account&Main'), 'primary'); ?></span>
<?php
  // Controle autorisation du client a ajouter des adresse dans son carnet selon la quantite ou sa fiche client
  if (AddressBook::countCustomerAddressBookEntries() < (int)MAX_ADDRESS_BOOK_ENTRIES && AddressBook::countCustomersAddAddress() == 1) {
?>
            <span class="col-md-6 text-md-right"><span class="buttonAction">
              <?php  echo HTML::button(CLICSHOPPING::getDef('button_add_address'), null, CLICSHOPPING::link(null, 'Account&AddressBookProcess&Create'), 'success'); ?>
            </span>
<?php
  }
?>
          </span>
        </div>
      </div>
    </div>
  </div>
</section>