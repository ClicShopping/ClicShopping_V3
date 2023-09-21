/*
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

//Display a message inside input fields
//address_book_details
//create_account_registration
//create_account_pro_registration
//guest account
const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
const tooltipList = tooltipTriggerList.map(tooltipTriggerEl => {
  return new Tooltip(tooltipTriggerEl);
});
