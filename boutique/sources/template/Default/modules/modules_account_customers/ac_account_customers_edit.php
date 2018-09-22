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

  use ClicShopping\Sites\Shop\Pages\Account\Classes\Edit;

  class ac_account_customers_edit {

    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_account_customers_edit_title');
      $this->description = CLICSHOPPING::getDef('module_account_customers_edit_description');

      if (defined('MODULE_ACCOUNT_CUSTOMERS_EDIT_TITLE_STATUS')) {
        $this->sort_order = MODULE_ACCOUNT_CUSTOMERS_EDIT_TITLE_SORT_ORDER;
        $this->enabled = MODULE_ACCOUNT_CUSTOMERS_EDIT_TITLE_STATUS;
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      $account = Edit::getAccountEdit();

      if (isset($_GET['Account']) && isset($_GET['Edit']) ) {

        $header_tag ='<link rel="stylesheet" type="text/css" href="ext/javascript/datepicker/less/datepicker.less">';
        $CLICSHOPPING_Template->addBlock($header_tag, $this->group);

        $footer_tag ='<script type="text/javascript" src="ext/javascript/datepicker/js/bootstrap-datepicker.js"></script>';
        $footer_tag .='<script src="' . $CLICSHOPPING_Template->getTemplateDefaultJavaScript('clicshopping/Shop/datepicker.js') . '"></script>';
        $CLICSHOPPING_Template->addBlock($footer_tag, 'footer_scripts');

        $content_width = (int)MODULE_ACCOUNT_CUSTOMERS_EDIT_CONTENT_WIDTH;

        if (!isset($_GET['AddressBookProcess'])) {
          $customers_gender = $account['customers_gender'];
          $customers_firstname = $account['customers_firstname'];
          $customers_lastname = $account['customers_lastname'];
          $customers_dob = $account['customers_dob'];
          $customers_email_address = $account['customers_email_address'];
          $customers_telephone = $account['customers_telephone'];
          $customers_cellular_phone = $account['customers_cellular_phone'];
          $customers_fax = $account['customers_fax'];
          $customers_company = $account['customers_company'];
          $customers_siret = $account['customers_siret'];
          $customers_ape = $account['customers_ape'];
          $customers_tva_intracom_code_iso = $account['customers_tva_intracom_code_iso'];
          $customers_tva_intracom = $account['customers_tva_intracom'];
        } else {
          $customers_gender = $entry['customers_gender'];
          $customers_firstname = $entry['customers_firstname'];
          $customers_lastname = $entry['customers_lastname'];
          $customers_dob = $entry['customers_dob'];
          $customers_email_address = $entry['customers_email_address'];
          $customers_telephone = $entry['customers_telephone'];
          $customers_cellular_phone = $entry['customers_cellular_phone'];
          $customers_fax = $entry['customers_fax'];
          $customers_company = $entry['customers_company'];
          $customers_siret = $entry['customers_siret'];
          $customers_ape = $entry['customers_ape'];
          $customers_tva_intracom_code_iso = $entry['customers_tva_intracom_code_iso'];
          $customers_tva_intracom = $entry['customers_tva_intracom'];
        }

        $account_edit = '<!-- Start account_customers_edit --> ' . "\n";

        $form = HTML::form('account_edit', CLICSHOPPING::link('index.php', 'Account&Edit&Process'), 'post', null,  ['tokenize' => true, 'action' => 'process']);
        $endform ='</form>';

        ob_start();
        require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/account_customers_edit'));
        $account_edit .= ob_get_clean();

        $account_edit .= '<!-- end account_customers_edit -->' . "\n";

        $CLICSHOPPING_Template->addBlock($account_edit, $this->group);

      } // php_self
    } // function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_ACCOUNT_CUSTOMERS_EDIT_TITLE_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULE_ACCOUNT_CUSTOMERS_EDIT_TITLE_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want activate this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the module',
          'configuration_key' => 'MODULE_ACCOUNT_CUSTOMERS_EDIT_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Select a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_ACCOUNT_CUSTOMERS_EDIT_TITLE_SORT_ORDER',
          'configuration_value' => '120',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '100',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
        ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
      );

    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_ACCOUNT_CUSTOMERS_EDIT_TITLE_STATUS',
        'MODULE_ACCOUNT_CUSTOMERS_EDIT_CONTENT_WIDTH',
        'MODULE_ACCOUNT_CUSTOMERS_EDIT_TITLE_SORT_ORDER'
      );
    }
  }
