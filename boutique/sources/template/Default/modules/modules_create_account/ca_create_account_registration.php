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
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Mail;

  class ca_create_account_registration {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_create_account_registration_title');
      $this->description = CLICSHOPPING::getDef('module_create_account_registration_description');

      if (defined('MODULE_CREATE_ACCOUNT_REGISTRATION_STATUS')) {
        $this->sort_order = MODULE_CREATE_ACCOUNT_REGISTRATION_SORT_ORDER;
        $this->enabled = (MODULE_CREATE_ACCOUNT_REGISTRATION_STATUS == 'True');
      }
    }

  public function execute() {
    $CLICSHOPPING_Template = Registry::get('Template');
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    if (isset($_GET['Account'] ) && isset($_GET['Create']) ) {
      $content_width = (int)MODULE_CREATE_ACCOUNT_REGISTRATION_CONTENT_WIDTH;

      $header_tag ='<link rel="stylesheet" type="text/css" href="ext/javascript/datepicker/less/datepicker.less">';
      $CLICSHOPPING_Template->addBlock($header_tag, $this->group);

      $footer_tag ='<script src="ext/javascript/datepicker/js/bootstrap-datepicker.js"></script>';
      $footer_tag .='<script src="' . $CLICSHOPPING_Template->getTemplateDefaultJavaScript('clicshopping/Shop/datepicker.js') . '"></script>';
      $CLICSHOPPING_Template->addBlock($footer_tag, 'footer_scripts');

      $create_account = '<!-- Start create_account_introduction start -->' . "\n";

      if (defined('MODULES_HEADER_TAGS_GOOGLE_RECAPTCHA_CREATE_ACCOUNT')) {
        if (MODULES_HEADER_TAGS_GOOGLE_RECAPTCHA_CREATE_ACCOUNT == 'True') {
          $google_recaptcha = $CLICSHOPPING_Hooks->output('AllShop', 'GoogleRecaptchaDisplay');
        }
      }
// number for the antispam
      $number_confirmation = Mail::getConfirmationNumberAntiSpam();

      $form =  HTML::form('create_account', CLICSHOPPING::link('index.php', 'Account&Create&Process'), 'post', 'id="create_account"',  ['tokenize' => true, 'action' => 'process']);
      $endform = '</form>';

      ob_start();
      require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/create_account_registration'));

      $create_account .= ob_get_clean();

      $create_account .= '<!-- End create_account_introduction  -->' . "\n";

      $CLICSHOPPING_Template->addBlock($create_account, $this->group);

    }
  }

  public function isEnabled() {
    return $this->enabled;
  }

  public function check() {
    return defined('MODULE_CREATE_ACCOUNT_REGISTRATION_STATUS');
  }

  public function install() {
    $CLICSHOPPING_Db = Registry::get('Db');

    $CLICSHOPPING_Db->save('configuration', [
        'configuration_title' => 'Do you want activate this module ?',
        'configuration_key' => 'MODULE_CREATE_ACCOUNT_REGISTRATION_STATUS',
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
        'configuration_key' => 'MODULE_CREATE_ACCOUNT_REGISTRATION_CONTENT_WIDTH',
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
          'configuration_key' => 'MODULE_CREATE_ACCOUNT_REGISTRATION_SORT_ORDER',
          'configuration_value' => '100',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '4',
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
      'MODULE_CREATE_ACCOUNT_REGISTRATION_STATUS',
      'MODULE_CREATE_ACCOUNT_REGISTRATION_CONTENT_WIDTH',
      'MODULE_CREATE_ACCOUNT_REGISTRATION_SORT_ORDER'
    );
  }
}
