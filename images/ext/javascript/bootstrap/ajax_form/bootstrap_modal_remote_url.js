/*
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

$('body').on('click', '[data-bs-toggle="modal"]', function () {
  $($(this).data("target") + ' .modal-body').load($(this).data("remote"));
});
