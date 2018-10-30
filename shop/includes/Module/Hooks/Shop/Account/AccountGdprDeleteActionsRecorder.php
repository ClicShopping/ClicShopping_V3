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

  namespace ClicShopping\OM\Module\Hooks\Shop\Account;

  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class AccountGdprDeleteActionsRecorder {

    public function display() {
        $output = '';
        $output .= '<div>';
        $output .= '<label class="checkbox-inline">';
        $output .= HTML::checkboxField('action_recorder');
        $output .= '</label>';
        $output .= CLICSHOPPING::getDef('module_account_customers_gdpr_delete_action_recorder');
        $output .= '</div>';

        return $output;
    }
  }
