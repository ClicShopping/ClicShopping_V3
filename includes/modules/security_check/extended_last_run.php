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
  use ClicShopping\OM\HTML;

  class securityCheck_extended_last_run
  {
    public string $type = 'warning';

    public function __construct()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      $CLICSHOPPING_Language->loadDefinitions('modules/security_check/extended_last_run', null, null, 'Shop');
    }

    public function pass()
    {

      $CLICSHOPPING_Db = Registry::get('Db');

      if (isset($_GET['SecurityCheck'])) {
        if (\defined('MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_DATETIME')) {
          $CLICSHOPPING_Db->save('configuration', [
            'configuration_value' => time(),
          ], [
              'configuration_key' => 'MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_DATETIME'
            ]
          );
        } else {

          $CLICSHOPPING_Db->save('configuration', [
              'configuration_title' => 'Security Check Extended Last Run',
              'configuration_key' => 'MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_DATETIME',
              'configuration_value' => time(),
              'configuration_description' => 'The date and time the last extended security check was performed.',
              'configuration_group_id' => '6',
              'sort_order' => '0',
              'set_function' => '',
              'date_added' => 'now()'
            ]
          );
        }

        return true;
      }

      return \defined('MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_DATETIME') && (MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_DATETIME > strtotime('-30 days'));
    }

    public function getMessage()
    {
      return HTML::link(CLICSHOPPING::link(null, 'A&Tools\SecurityCheck&SecurityCheck'), CLICSHOPPING::getDef('module_security_check_extended_last_run_old'));
    }
  }

