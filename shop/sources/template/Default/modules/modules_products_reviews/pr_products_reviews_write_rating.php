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
  use ClicShopping\Sites\Common\HTMLOverrideCommon;

  class pr_products_reviews_write_rating {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('modules_products_reviews_write_rating_title');
      $this->description = CLICSHOPPING::getDef('modules_products_reviews_write_rating_description');

      if ( defined('MODULES_PRODUCTS_REVIEWS_WRITE_RATING_STATUS') ) {
        $this->sort_order = MODULES_PRODUCTS_REVIEWS_WRITE_RATING_SORT_ORDER;
        $this->enabled = (MODULES_PRODUCTS_REVIEWS_WRITE_RATING_STATUS == 'True');
      }
    }

    public function execute() {

      if (isset($_GET['Products']) && isset($_GET['ReviewsWrite']) && !isset($_GET['Success'])) {
        $CLICSHOPPING_Template = Registry::get('Template');

        $content_width = (int)MODULES_PRODUCTS_REVIEWS_WRITE_RATING_CONTENT_WIDTH;
        $text_position = MODULES_PRODUCTS_REVIEWS_WRITE_RATING_POSITION;

        $data = '<!-- pr_products_reviews_write start -->' . "\n";

        $data .= HTMLOverrideCommon::starHeaderTagRateYo();
        $rating = HTMLOverrideCommon::starTagRateYo(null, null, false, '40');

        ob_start();
        require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/products_reviews_write_rating'));

        $data .= ob_get_clean();
        $data .= '<!-- pr_products_reviews_write end -->' . "\n";

        $CLICSHOPPING_Template->addBlock($data, $this->group);
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULES_PRODUCTS_REVIEWS_WRITE_RATING_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULES_PRODUCTS_REVIEWS_WRITE_RATING_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the module',
          'configuration_key' => 'MODULES_PRODUCTS_REVIEWS_WRITE_RATING_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Select a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez selectionner la couleur des étoiles ?',
          'configuration_key' => 'MODULES_PRODUCTS_REVIEWS_WRITE_RATING_COLOR',
          'configuration_value' => '#A0A0A0',
          'configuration_description' => 'Veuillez indiquer une couleur de type #A0A0A0 (hexadecimal)',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
              'configuration_title' => 'Where do you want display the module ?',
              'configuration_key' => 'MODULES_PRODUCTS_REVIEWS_WRITE_RATING_POSITION',
              'configuration_value' => 'float-md-left',
              'configuration_description' => 'Select where you want display the module',
              'configuration_group_id' => '6',
              'sort_order' => '2',
              'set_function' => 'clic_cfg_set_boolean_value(array(\'float-md-right\', \'float-md-left\', \'float-md-none\'))',
              'date_added' => 'now()'
          ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULES_PRODUCTS_REVIEWS_WRITE_RATING_SORT_ORDER',
          'configuration_value' => '500',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
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
      return array('MODULES_PRODUCTS_REVIEWS_WRITE_RATING_STATUS',
                   'MODULES_PRODUCTS_REVIEWS_WRITE_RATING_CONTENT_WIDTH',
                   'MODULES_PRODUCTS_REVIEWS_WRITE_RATING_POSITION',
                   'MODULES_PRODUCTS_REVIEWS_WRITE_RATING_COLOR',
                   'MODULES_PRODUCTS_REVIEWS_WRITE_RATING_SORT_ORDER'
                  );
    }
  }
