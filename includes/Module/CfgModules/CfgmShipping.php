<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class CfgmShipping
  {
    public string $code = 'shipping';
    public string $directory;
    public $language_directory;
    public string $site = 'Shop';
    public string $key = 'MODULE_SHIPPING_INSTALLED';
    public $title;
    public bool $template_integration = false;

    public function __construct()
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      $this->directory = $CLICSHOPPING_Template->getDirectoryPathModuleShop() . '/shipping/';
      $this->language_directory = $CLICSHOPPING_Template->getPathLanguageShopDirectory();
    }
  }