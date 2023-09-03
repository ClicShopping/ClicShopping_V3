<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\Shop\Pages\Account\Actions\AddressBookProcess;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;
use ClicShopping\Sites\Shop\AddressBook;

class Delete extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {

    $CLICSHOPPING_Customer = Registry::get('Customer');
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    if (!isset($_GET['delete']) && is_numeric($_GET['delete'])) {
      if ($_GET['delete'] == $CLICSHOPPING_Customer->getDefaultAddressID()) {
        $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('warning_primary_address_deletion'), 'error');

        CLICSHOPPING::redirect(null, 'Account&AddressBook');
      }

      if (AddressBook::countCustomerAddressBookEntries() >= (int)MAX_ADDRESS_BOOK_ENTRIES) {
        $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_address_book_full'), 'error');

        CLICSHOPPING::redirect(null, 'Account&AddressBook');
      } elseif (AddressBook::countCustomersAddAddress() == 0) {
        $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_address_book_no_add'), 'error');

        CLICSHOPPING::redirect(null, 'Account&AddressBook');
      }
    }

    if (isset($_GET['action']) && ($_GET['action'] == 'deleteconfirm') && isset($_GET['delete']) && is_numeric($_GET['delete']) && isset($_GET['formid']) && ($_GET['formid'] == md5($_SESSION['sessiontoken']))) {
      if ($_GET['delete'] == $CLICSHOPPING_Customer->get('default_address_id')) {
        $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('warning_primary_address_deletion'), 'error');
      } else {
        AddressBook::deleteEntry($_GET['delete']);
        $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('success_address_book_entry_deleted'), 'error');
      }

      $CLICSHOPPING_Hooks->call('AddressBookProcess', 'DeleteConfirm');

      CLICSHOPPING::redirect(null, 'Account&AddressBook');
    }
  }
}