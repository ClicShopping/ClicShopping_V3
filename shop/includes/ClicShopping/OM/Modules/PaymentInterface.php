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

  namespace ClicShopping\OM\Modules;

  interface PaymentInterface {
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
