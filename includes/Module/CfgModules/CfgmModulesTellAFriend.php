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

  class CfgmModulesTellAFriend
  {
    public string $code = 'modules_tell_a_friend';
    public string $directory;
    public $language_directory;
    public string $site = 'Shop';
    public string $key = 'MODULE_MODULES_TELL_A_FRIEND_INSTALLED';
    public $title;
    public bool $template_integration = true;

    public function __construct()
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      $this->directory = $CLICSHOPPING_Template->getDirectoryPathShopDefaultTemplateHtml() . '/modules/modules_tell_a_friend/';
      $this->language_directory = $CLICSHOPPING_Template->getPathLanguageShopDirectory();

      $this->title = CLICSHOPPING::getDef('module_cfg_module_tell_a_friend_title');
    }
  }

