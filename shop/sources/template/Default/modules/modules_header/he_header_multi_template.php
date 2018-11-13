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
  use ClicShopping\Apps\Catalog\Categories\Classes\Shop\CategoryTree;

  class he_header_multi_template {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;
    public $pages;

    public function __construct()  {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('modules_header_multi_template_title');
      $this->description = CLICSHOPPING::getDef('modules_header_multi_template_description');

      if (defined('MODULES_HEADER_MULTI_TEMPLATE_STATUS')) {
        $this->sort_order = MODULES_HEADER_MULTI_TEMPLATE_SORT_ORDER;
        $this->enabled = (MODULES_HEADER_MULTI_TEMPLATE_STATUS == 'True');
        $this->pages = MODULES_HEADER_MULTI_TEMPLATE_TEMPLATE_DISPLAY_PAGES;
      }
    }

    public function execute()  {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_CategoryTree = Registry::get('CategoryTree');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Service = Registry::get('Service');
      $CLICSHOPPING_Banner = Registry::get('Banner');
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');
      $CLICSHOPPING_Currencies = Registry::get('Currencies');
      $CLICSHOPPING_Tax = Registry::get('Tax');

      $CLICSHOPPING_CategoryTree->reset();
      $CLICSHOPPING_CategoryTree->setMaximumLevel(1);
      $CLICSHOPPING_CategoryTree->setParentGroupString('<ul class="TemplateHeaderCategoriesNavigation">', '</ul>', true);
      $CLICSHOPPING_CategoryTree->setChildString('<li class="TemplateHeaderCategoriesNavigation">', '</li>');

      $languages_string = $CLICSHOPPING_Language->getFlag();
      $content_width = (int)MODULES_HEADER_MULTI_TEMPLATE_TEMPLATE_CONTENT_WIDTH;
      $login = HTML::button(CLICSHOPPING::getDef('modules_header_multi_template_account_login'), null, null, 'primary', null, 'sm');

      $form_advanced_result = HTML::form('quick_find', CLICSHOPPING::link(null, 'Search&Q'), 'post', 'id="quick_find"', ['session_id' => true]);
      $form = HTML::form('loginForm',  CLICSHOPPING::link(null, 'Account&LogIn&Process'), 'post', 'id="loginForm"', ['tokenize' => true]);
      $endform = '</form>';

      if ($CLICSHOPPING_Service->isStarted('Banner') ) {
        if ($banner = $CLICSHOPPING_Banner->bannerExists('dynamic',  MODULES_HEADER_MULTI_MODULE_LOGO_BANNER_GROUP)) {
          $logo_header = $CLICSHOPPING_Banner->displayBanner('static', $banner) . '<br /><br />';
        }

        if ($banner = $CLICSHOPPING_Banner->bannerExists('dynamic',  MODULES_HEADER_MULTI_MODULE_BANNER_2_GROUP)) {
          $banner_header = $CLICSHOPPING_Banner->displayBanner('static', $banner) . '<br /><br />';
        }
      }

      if ($CLICSHOPPING_ShoppingCart->getCountContents() > 0) {
        $shopping_cart = $CLICSHOPPING_ShoppingCart->getCountContents() . '&nbsp;' . CLICSHOPPING::getDef('modules_header_multi_template_shopping_cart_product') . '&nbsp;';
      } else {
        $shopping_cart = CLICSHOPPING::getDef('modules_header_multi_template_shopping_cart_no_products');
      }

      if (substr(CLICSHOPPING::getBaseNameIndex(), 0, 8) != 'checkout') {
        $currency_header = $CLICSHOPPING_Currencies->getCurrenciesDropDown('headerMultiTemplateDefaultCurrencies');
      }

      $header_template = '<!-- header template start -->' . "\n";

      $filename = '';
      $filename = $CLICSHOPPING_Template->getTemplateModulesFilename($this->group . '/template_html/' . MODULES_HEADER_MULTI_TEMPLATE_FILES);

      if (is_file($filename)) {
        ob_start();
        require($filename);
        $header_template .= ob_get_clean();
      } else {
        echo  '<div class="alert alert-warning text-md-center" role="alert">' . CLICSHOPPING::getDef('template_does_not_exist') . '</div>';
        exit;
      }

      $header_template .= '<!-- header template end -->' . "\n";

      $CLICSHOPPING_Template->addBlock($header_template, $this->group);
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULES_HEADER_MULTI_TEMPLATE_STATUS');
    }

    public function install()  {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULES_HEADER_MULTI_TEMPLATE_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want activate this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please, select the width of your module ?',
          'configuration_key' => 'MODULES_HEADER_MULTI_TEMPLATE_TEMPLATE_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Indicate a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the template you want to use ?',
          'configuration_key' => 'MODULES_HEADER_MULTI_TEMPLATE_FILES',
          'configuration_value' => 'multi_template_default.php',
          'configuration_description' => 'Select the the template you want to use.',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_multi_template_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the banner group for the logo',
          'configuration_key' => 'MODULES_HEADER_MULTI_MODULE_LOGO_BANNER_GROUP',
          'configuration_value' => SITE_THEMA . '_multi_template_logo',
          'configuration_description' => 'Indicate the banner group<br /><br /><strong>Note :</strong><br /><i>The group must be created or selected whtn you create a banner in Marketing / banner</i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the banner group for this image 2',
          'configuration_key' => 'MODULES_HEADER_MULTI_MODULE_BANNER_2_GROUP',
          'configuration_value' => SITE_THEMA . '_multi_template_banner',
          'configuration_description' => 'Indicate the banner group<br /><br /><strong>Note :</strong><br /><i>The group must be created or selected whtn you create a banner in Marketing / banner</i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULES_HEADER_MULTI_TEMPLATE_SORT_ORDER',
          'configuration_value' => '100',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Indicate the page where the module is displayed',
          'configuration_key' => 'MODULES_HEADER_MULTI_TEMPLATE_TEMPLATE_DISPLAY_PAGES',
          'configuration_value' => 'all',
          'configuration_description' => 'Select the page where the module is displayed',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => 'clic_cfg_set_select_pages_list',
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
      return array('MODULES_HEADER_MULTI_TEMPLATE_STATUS',
                    'MODULES_HEADER_MULTI_TEMPLATE_TEMPLATE_CONTENT_WIDTH',
                    'MODULES_HEADER_MULTI_TEMPLATE_FILES',
                    'MODULES_HEADER_MULTI_MODULE_LOGO_BANNER_GROUP',
                    'MODULES_HEADER_MULTI_MODULE_BANNER_2_GROUP',
                    'MODULES_HEADER_MULTI_TEMPLATE_SORT_ORDER',
                    'MODULES_HEADER_MULTI_TEMPLATE_TEMPLATE_DISPLAY_PAGES'
                  );
    }
  }
