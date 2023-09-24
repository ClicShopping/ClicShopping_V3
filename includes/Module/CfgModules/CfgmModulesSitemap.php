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

class CfgmModulesSitemap
{
  public string $code = 'modules_sitemap';
  public string $directory;
  public $language_directory;
  public string $site = 'Shop';
  public string $key = 'MODULE_MODULES_SITEMAP_INSTALLED';
  public $title;
  public bool $template_integration = true;

  public function __construct()
  {
    $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

    $this->directory = $CLICSHOPPING_Template->getDirectoryPathShopDefaultTemplateHtml() . '/modules/modules_sitemap/';
    $this->language_directory = $CLICSHOPPING_Template->getPathLanguageShopDirectory();

    $this->title = CLICSHOPPING::getDef('module_cfg_module_sitemap_title');
  }
}

