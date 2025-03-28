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

class co_contact_us_privacy_condition
{
  public string $code;
  public string $group;
  public $title;
  public $description;
  public int|null $sort_order = 0;
  public bool $enabled = false;

  public function __construct()
  {
    $this->code = get_class($this);
    $this->group = basename(__DIR__);

    $this->title = CLICSHOPPING::getDef('modules_contact_us_privacy_condition_title');
    $this->description = CLICSHOPPING::getDef('modules_contact_us_privacy_condition_description');

    if (\defined('MODULES_CONTACT_US_PRIVACY_CONDITION_STATUS')) {
      $this->sort_order = (int)MODULES_CONTACT_US_PRIVACY_CONDITION_SORT_ORDER ?? 0;
      $this->enabled = (MODULES_CONTACT_US_PRIVACY_CONDITION_STATUS == 'True');
    }
  }

  public function execute()
  {
    $CLICSHOPPING_Template = Registry::get('Template');

    if (isset($_GET['Info'], $_GET['Contact']) && !isset($_GET['Success'])) {
      $content_width = (int)MODULES_CONTACT_US_PRIVACY_CONDITION_CONTENT_WIDTH;

      if (DISPLAY_PRIVACY_CONDITIONS == 'true') {
        $contact_us_privacy_condition = '<!--  contact_us_privacy_condition start -->' . "\n";

        ob_start();
        require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/contact_us_privacy_condition'));

        $contact_us_privacy_condition .= ob_get_clean();

        $contact_us_privacy_condition .= '<!-- contact_us_privacy_condition end -->' . "\n";

        $CLICSHOPPING_Template->addBlock($contact_us_privacy_condition, $this->group);
      }
    }
  }

  public function isEnabled()
  {
    return $this->enabled;
  }

  public function check()
  {
    return \defined('MODULES_CONTACT_US_PRIVACY_CONDITION_STATUS');
  }

  public function install()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $CLICSHOPPING_Db->save('configuration', [
        'configuration_title' => 'Do you want to enable this module ?',
        'configuration_key' => 'MODULES_CONTACT_US_PRIVACY_CONDITION_STATUS',
        'configuration_value' => 'True',
        'configuration_description' => 'Do you want to enable this module in your shop ?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
        'date_added' => 'now()'
      ]
    );

    $CLICSHOPPING_Db->save('configuration', [
        'configuration_title' => 'Please select the width of the module',
        'configuration_key' => 'MODULES_CONTACT_US_PRIVACY_CONDITION_CONTENT_WIDTH',
        'configuration_value' => '12',
        'configuration_description' => 'Select a number between 1 and 12',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'clic_cfg_set_content_module_width_pull_down',
        'date_added' => 'now()'
      ]
    );


    $CLICSHOPPING_Db->save('configuration', [
        'configuration_title' => 'Sort order',
        'configuration_key' => 'MODULES_CONTACT_US_PRIVACY_CONDITION_SORT_ORDER',
        'configuration_value' => '450',
        'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
        'configuration_group_id' => '6',
        'sort_order' => '10',
        'set_function' => '',
        'date_added' => 'now()'
      ]
    );
  }

  public function remove()
  {
    return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
  }

  public function keys()
  {
    return array('MODULES_CONTACT_US_PRIVACY_CONDITION_STATUS',
      'MODULES_CONTACT_US_PRIVACY_CONDITION_CONTENT_WIDTH',
      'MODULES_CONTACT_US_PRIVACY_CONDITION_SORT_ORDER'
    );
  }
}
