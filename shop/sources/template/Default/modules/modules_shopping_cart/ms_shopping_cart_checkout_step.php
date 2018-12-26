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


  class ms_shopping_cart_checkout_step {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_shopping_cart_checkout_step_title');
      $this->description = CLICSHOPPING::getDef('module_shopping_cart_checkout_step_description');

      if (defined('MODULE_SHOPPING_CART_CHECKOUT_STEP_STATUS')) {
        $this->sort_order = MODULE_SHOPPING_CART_CHECKOUT_STEP_SORT_ORDER;
        $this->enabled = (MODULE_SHOPPING_CART_CHECKOUT_STEP_STATUS == 'True');
      }
    }

    public function execute() {

      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');

      if (isset($_GET['Cart'])  && $CLICSHOPPING_ShoppingCart->getCountContents() > 0) {

        $content_width = (int)MODULE_SHOPPING_CART_CHECKOUT_STEP_CONTENT_WIDTH;

        $shopping_cart_information_customers = '  <!-- start ms_shopping_cart_step -->'. "\n";

        ob_start();
        require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/shopping_cart_checkout_step'));

        $shopping_cart_information_customers .= ob_get_clean();

        $shopping_cart_information_customers .= '<!-- end ms_shopping_cart_step -->' . "\n";

        $CLICSHOPPING_Template->addBlock($shopping_cart_information_customers, $this->group);
      }
    } // function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_SHOPPING_CART_CHECKOUT_STEP_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_SHOPPING_CART_CHECKOUT_STEP_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module à votre boutique ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez selectionner la largeur de l\'affichage?',
          'configuration_key' => 'MODULE_SHOPPING_CART_CHECKOUT_STEP_CONTENT_WIDTH',
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
          'configuration_key' => 'MODULE_SHOPPING_CART_CHECKOUT_STEP_SORT_ORDER',
          'configuration_value' => '200',
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
        'MODULE_SHOPPING_CART_CHECKOUT_STEP_STATUS',
        'MODULE_SHOPPING_CART_CHECKOUT_STEP_CONTENT_WIDTH',
        'MODULE_SHOPPING_CART_CHECKOUT_STEP_SORT_ORDER'
      );
    }
  }
