<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class CfgmModulesProductsSearch
{
  public string $code = 'modules_products_search';
  public string $directory;
  public $language_directory;
  public string $site = 'Shop';
  public string $key = 'MODULE_MODULES_PRODUCTS_SEARCH_INSTALLED';
  public $title;
  public bool $template_integration = true;

  /**
   * Constructor method for initializing the module with directory and language paths,
   * and setting the title for the product search module configuration.
   *
   * @return void
   */
  public function __construct()
  {
    $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

    $this->directory = $CLICSHOPPING_Template->getDirectoryPathShopDefaultTemplateHtml() . '/modules/modules_products_search/';
    $this->language_directory = $CLICSHOPPING_Template->getPathLanguageShopDirectory();

    $this->title = CLICSHOPPING::getDef('module_cfg_module_products_search_modules_title');
  }
}

