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

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class securityCheck_check_github_directory {
    public string $type = 'warning';

    public function __construct()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      $CLICSHOPPING_Language->loadDefinitions('modules/security_check/check_github_directory', null, null, 'Shop');

      $this->title = CLICSHOPPING::getDef('check_github_directory_title');
    }

    public function pass()
    {
      return !is_dir(CLICSHOPPING::getConfig('dir_root', 'Shop') . '.github');
    }

    public function getMessage()
    {
      return CLICSHOPPING::getDef('warning_github_directory_exists', [
        'github_pah' => CLICSHOPPING::getConfig('dir_root', 'Shop') . '.github'
      ]);
    }
  }
  