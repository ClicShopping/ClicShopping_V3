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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class bm_currencies {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;
    public $pages;

    public function  __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);
      $this->title = CLICSHOPPING::getDef('module_boxes_currencies_title');
      $this->description = CLICSHOPPING::getDef('module_boxes_currencies_description');

      if ( defined('MODULE_BOXES_CURRENCIES_STATUS') ) {
        $this->sort_order = MODULE_BOXES_CURRENCIES_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_CURRENCIES_STATUS == 'True');
        $this->pages = MODULE_BOXES_CURRENCIES_DISPLAY_PAGES;
        $this->group = ((MODULE_BOXES_CURRENCIES_CONTENT_PLACEMENT == 'Left Column') ? 'boxes_column_left' : 'boxes_column_right');
      }
    }

    public function  execute() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Currencies = Registry::get('Currencies');
      $CLICSHOPPING_Service = Registry::get('Service');
      $CLICSHOPPING_Banner = Registry::get('Banner');

      if (!isset($_GET['Checkout'])) {
        $currencies_banner = '';

        if ($CLICSHOPPING_Service->isStarted('Banner') ) {
          if ($banner = $CLICSHOPPING_Banner->bannerExists('dynamic',  MODULE_BOXES_CURRENCIES_BANNER_GROUP)) {
            $currencies_banner = $CLICSHOPPING_Banner->displayBanner('static', $banner) . '<br /><br />';
          } else {
            $currencies_banner = '';
          }
        }

        $output = $CLICSHOPPING_Currencies->getCurrenciesDropDown('boxePullDownsideCurrencies');

        $currency ='<!-- Boxe currencies start -->' . "\n";

        ob_start();
        require_once($CLICSHOPPING_Template->getTemplateModules('/modules_boxes/content/currencies'));
        $currency .= ob_get_clean();

        $currency .='<!-- Boxe currencies end -->' . "\n";

        $CLICSHOPPING_Template->addBlock($currency, $this->group);
      }
    }

    public function  isEnabled() {
      return $this->enabled;
    }

    public function  check() {
      return defined('MODULE_BOXES_CURRENCIES_STATUS');
    }

    public function  install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULE_BOXES_CURRENCIES_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want activate this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please choose where the boxe must be displayed',
          'configuration_key' => 'MODULE_BOXES_CURRENCIES_CONTENT_PLACEMENT',
          'configuration_value' => 'Right Column',
          'configuration_description' => 'Choose where the boxe must be displayed',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'Left Column\', \'Right Column\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the banner group for the image',
          'configuration_key' => 'MODULE_BOXES_CURRENCIES_BANNER_GROUP',
          'configuration_value' => SITE_THEMA.'_boxe_currencies',
          'configuration_description' => 'Indicate the banner group<br /><br /><strong>Note :</strong><br /><i>The group must be created or selected whtn you create a banner in Marketing / banner</i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_BOXES_CURRENCIES_SORT_ORDER',
          'configuration_value' => '120',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Indicate the page where the module is displayed',
          'configuration_key' => 'MODULE_BOXES_CURRENCIES_DISPLAY_PAGES',
          'configuration_value' => 'all',
          'configuration_description' => 'Select the page where the module is displayed.',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => 'clic_cfg_set_select_pages_list',
          'date_added' => 'now()'
        ]
      );


      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                               ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
                              );

    }

    public function  remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function  keys() {
      return array('MODULE_BOXES_CURRENCIES_STATUS',
                   'MODULE_BOXES_CURRENCIES_CONTENT_PLACEMENT',
                   'MODULE_BOXES_CURRENCIES_BANNER_GROUP',
                   'MODULE_BOXES_CURRENCIES_SORT_ORDER',
                   'MODULE_BOXES_CURRENCIES_DISPLAY_PAGES');
    }
  }
