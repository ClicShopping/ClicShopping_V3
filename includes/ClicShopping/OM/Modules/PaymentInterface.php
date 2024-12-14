<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Modules;

/**
 * Interface for implementing payment modules.
 *
 * Defines the methods that must be implemented to handle various
 * stages of a payment process, such as status updates, validation,
 * and error handling.
 */
interface PaymentInterface
{
  public function update_status();

  public function javascript_validation();

  public function selection();

  public function pre_confirmation_check();

  public function confirmation();

  public function process_button();

  public function before_process();

  public function after_process();

  public function get_error();

  public function check();

  public function install();

  public function remove();

  public function keys();
}
