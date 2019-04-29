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
  use ClicShopping\Sites\Shop\AddressBook;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Customer = Registry::get('Customer');
  $CLICSHOPPING_NavigationHistory = Registry::get('NavigationHistory');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  if ($CLICSHOPPING_MessageStack->exists('addressbook')) {
    echo $CLICSHOPPING_MessageStack->get('addressbook');
  }

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

  if (!isset($_GET['delete']) && isset($_GET['edit'])) {

    if (isset($_GET['newcustomer'])) {
     $newcustomer = '&newcustomer=' . 1;
    }

    $entry = AddressBook::getEntry((int)$_GET['edit']);

    echo HTML::form('addressbook', CLICSHOPPING::link(null, 'Account&AddressBookProcess' . (isset($_GET['edit']) ? '&Edit&edit=' . $_GET['edit'] : '') . $newcustomer), 'post', 'id="addressbook"',  ['tokenize' => true]);
  } else if (!isset($_GET['delete']) && !isset($_POST['edit'])) {
    echo HTML::form('addressbook', CLICSHOPPING::link(null, 'Account&AddressBookProcess&Create&action=process'), 'post', 'id="addressbook"',  ['tokenize' => true]);
  }
?>
<section class="address_book_process" id="address_book_process">
  <div class="contentContainer">
    <div class="contentText">
      <h2><?php if (isset($_GET['edit'])) { echo CLICSHOPPING::getDef('heading_title_modify_entry'); } elseif (isset($_GET['delete'])) { echo CLICSHOPPING::getDef('heading_title_delete_entry'); } else { echo CLICSHOPPING::getDef('heading_title_add_entry'); } ?></h2>
<?php
// -------------------------------
// --- Delete Adresse Title   -----
// ------------------------------

  if (isset($_GET['delete'])) {
?>
      <div class="separator"></div>
      <span class="col-md-9"> <strong><?php echo CLICSHOPPING::getDef('delete_address_description'); ?></strong></span>
      <span class="col-md-3">
        <div>
          <p><strong><?php echo CLICSHOPPING::getDef('selected_address'); ?></strong></p>
        </div>
        <div class="separator"></div>
        <div>
          <?php echo AddressBook::addressLabel($CLICSHOPPING_Customer->getID(), $_GET['delete'], true, ' ', '<br />'); ?>
        </div>
      </span>
<?php
// ----------------------
// --- Button   -----
// ----------------------
?>

      <div class="separator"></div>
      <div class="buttonSet">
        <div class="col-md-6 float-md-left"><?php echo HTML::button(CLICSHOPPING::getDef('button_back'), null, CLICSHOPPING::link(null,'Account&AddressBook'), 'primary'); ?></div>
        <div class="col-md-6 float-md-right"><span class="buttonAction"><?php echo HTML::button(CLICSHOPPING::getDef('button_delete'), null, CLICSHOPPING::link(null, 'Account&AddressBookProcess&Delete&delete=' . $_GET['delete'] . '&action=deleteconfirm&formid=' . md5($_SESSION['sessiontoken'])), 'success'); ?></span></div>
      </div>
<?php
  } else {
// ----------------------
//  Address Detail
// ----------------------
  require_once($CLICSHOPPING_Template->getTemplateModules('customers_address/address_book_details'));

  if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
?>
      <div class="contentText">
<?php
     if (!isset($_GET['newcustomer'])) {
?>
        <div class="col-md-6"><?php echo HTML::button(CLICSHOPPING::getDef('button_back'), null, CLICSHOPPING::link(null,'Account&AddressBook'), 'primary'); ?></div>
<?php
     }
?>
       <div class="col-md-6 float-md-right" align="right"><?php echo HTML::hiddenField('action', 'update') . HTML::hiddenField('edit', (int)$_GET['edit']) .  HTML::hiddenField('shopping', (int)$_GET['shopping']) . HTML::button(CLICSHOPPING::getDef('button_update'), 'refresh', null, 'success'); ?></div>
    </div>
    <div class="clearfix"></div>
<?php
    } else {
      if (count($CLICSHOPPING_NavigationHistory->snapshot) > 0) {
        $back_link = CLICSHOPPING::link($CLICSHOPPING_NavigationHistory->snapshot['page'], CLICSHOPPING::ArrayToString($CLICSHOPPING_NavigationHistory->snapshot['get'], array(session_name())), $CLICSHOPPING_NavigationHistory->snapshot['mode']);
      } else {
        $back_link = CLICSHOPPING::link(null,'Account&AddressBook');
      }
// ----------------------
// --- Button   -----
// ----------------------
?>
      <div class="control-group">
        <div class="controls">
          <div class="buttonSet">
            <span class="col-md-6"><?php echo HTML::button(CLICSHOPPING::getDef('button_back'), null, $back_link, 'primary'); ?></span>
            <span class="col-md-6 text-md-right"><span class="buttonAction">
            <?php echo  HTML::hiddenField('action', 'process') . HTML::button(CLICSHOPPING::getDef('button_continue'), null, null, 'success'); ?></span></span>
          </div>
        </div>
      </div>
<?php
    }
?>
    </form>
<?php
  }
?>
    </div>
  </div>
</section>