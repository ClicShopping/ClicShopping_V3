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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class ml_login_customer_privacy {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_login_customer_privacy');
      $this->description = CLICSHOPPING::getDef('module_login_customer_privacy_description');

      if (defined('MODULE_LOGIN_CUSTOMER_PRIVACY_STATUS')) {
        $this->sort_order = MODULE_LOGIN_CUSTOMER_PRIVACY_SORT_ORDER;
        $this->enabled = (MODULE_LOGIN_CUSTOMER_PRIVACY_STATUS == 'True');
      }
     }

    public function execute() {

      $CLICSHOPPING_Template = Registry::get('Template');

      if (isset($_GET['Account']) && isset($_GET['LogIn'])) {

        $content_width = (int)MODULE_LOGIN_CUSTOMER_PRIVACY_CONTENT_WIDTH;

        $login_information_customers = '<!-- login_customer_privacy start -->'. "\n";

        ob_start();
        require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/login_customer_privacy'));

        $login_information_customers .= ob_get_clean();

        $login_information_customers .= '<!-- login_customer_privacy  end-->' . "\n";

        $CLICSHOPPING_Template->addBlock($login_information_customers, $this->group);
      }
    } // function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_LOGIN_CUSTOMER_PRIVACY_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_LOGIN_CUSTOMER_PRIVACY_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez selectionner la largeur de l\'affichage?',
          'configuration_key' => 'MODULE_LOGIN_CUSTOMER_PRIVACY_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Veuillez indiquer un nombre compris entre 1 et 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_LOGIN_CUSTOMER_PRIVACY_SORT_ORDER',
          'configuration_value' => '7',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
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
        'MODULE_LOGIN_CUSTOMER_PRIVACY_STATUS',
        'MODULE_LOGIN_CUSTOMER_PRIVACY_CONTENT_WIDTH',
        'MODULE_LOGIN_CUSTOMER_PRIVACY_SORT_ORDER'
      );
    }
  }
