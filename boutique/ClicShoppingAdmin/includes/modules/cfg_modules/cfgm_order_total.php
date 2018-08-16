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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class cfgm_order_total {
    public $code = 'order_total';
    public $directory;
    public $language_directory;
    public $site = 'Shop';
    public $key = 'MODULE_ORDER_TOTAL_INSTALLED';
    public $title;
    public $template_integration = false;

    public function __construct() {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      $this->directory = $CLICSHOPPING_Template->getDirectoryPathModuleShop() . '/order_total/';
      $this->language_directory = $CLICSHOPPING_Template->getPathLanguageShopDirectory();

      $this->title = CLICSHOPPING::getDef('module_cfg_module_order_total_title');
    }
  }