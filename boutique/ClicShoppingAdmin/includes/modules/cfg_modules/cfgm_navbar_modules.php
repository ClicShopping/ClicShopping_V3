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

  class cfgm_navbar_modules {
    public $code = 'navbar_modules';
    public $directory;
    public $language_directory;
    public $site = 'Shop';
    public $key = 'MODULE_CONTENT_NAVBAR_INSTALLED';
    public $title;
    public $template_integration = false;

    public function __construct() {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

//      $this->directory = CLICSHOPPING::getConfig('dir_root', $this->site) . 'includes/modules/navbar_modules/';
//      $this->language_directory = CLICSHOPPING::getConfig('dir_root', $this->site) . 'includes/languages/';

      $this->directory = $CLICSHOPPING_Template->getDirectoryPathModuleShop() . '/navbar_modules/';
      $this->language_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/languages/';
      $this->title = CLICSHOPPING::getDef('module_cfg_module_content_navbar_title');
    }
  }