<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class CfgmActionRecorder
  {

    public string $code = 'action_recorder';
    public string $directory;
    public $language_directory;
    public string $site = 'Shop';
    public string $key = 'MODULE_ACTION_RECORDER_INSTALLED';
    public $title;
    public bool $template_integration = false;

    public function __construct()
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      $this->directory = $CLICSHOPPING_Template->getDirectoryPathModuleShop() . '/action_recorder/';
      $this->language_directory = $CLICSHOPPING_Template->getPathLanguageShopDirectory();

      $this->title = CLICSHOPPING::getDef('module_cfg_module_action_recorder_title');
    }
  }