<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class securityCheck_session_storage
  {
    public string $type = 'warning';

    public function __construct()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      $CLICSHOPPING_Language->loadDefinitions('modules/SecurityCheck/session_storage', null, null, 'Shop');

    }

    public function pass()
    {
      return ((CLICSHOPPING::getConfig('store_sessions') != '') || FileSystem::isWritable(session_save_path()));
    }

    public function getMessage()
    {
      if (CLICSHOPPING::getConfig('store_sessions') == '') {
        if (!is_dir(session_save_path())) {
          return CLICSHOPPING::getDef('warning_session_directory_non_existent', [
            'session_path' => session_save_path()
          ]);
        } elseif (!FileSystem::isWritable(session_save_path())) {
          return CLICSHOPPING::getDef('warning_session_directory_not_writeable', [
            'session_path' => session_save_path()
          ]);
        }
      }
    }
  }