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

class si_sitemap_summary
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

    $this->title = CLICSHOPPING::getDef('modules_sitemap_summary_title');
    $this->description = CLICSHOPPING::getDef('modules_sitemap_summary_description');

    if (\defined('MODULES_SITEMAP_SUMMARY_STATUS')) {
      $this->sort_order = (int)MODULES_SITEMAP_SUMMARY_SORT_ORDER ?? 0;
      $this->enabled = (MODULES_SITEMAP_SUMMARY_STATUS == 'True');
    }
  }

  public function execute()
  {
    $CLICSHOPPING_Template = Registry::get('Template');
    $content_width = (int)MODULES_SITEMAP_SUMMARY_CONTENT_WIDTH;

    // essentiel sinon conflit
    if (isset($_GET['SiteMap'])) {

      $sitemap_summary = '<!-- sitemap summary start -->' . "\n";

      ob_start();
      require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/sitemap_summary'));

      $sitemap_summary .= ob_get_clean();

      $sitemap_summary .= '<!-- sitemap summary end -->' . "\n";

      $CLICSHOPPING_Template->addBlock($sitemap_summary, $this->group);
    }
  } // public function execute

  public function isEnabled()
  {
    return $this->enabled;
  }

  public function check()
  {
    return \defined('MODULES_SITEMAP_SUMMARY_STATUS');
  }

  public function install()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $CLICSHOPPING_Db->save('configuration', [
        'configuration_title' => 'Do you want to enable this module ?',
        'configuration_key' => 'MODULES_SITEMAP_SUMMARY_STATUS',
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
        'configuration_key' => 'MODULES_SITEMAP_SUMMARY_CONTENT_WIDTH',
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
        'configuration_key' => 'MODULES_SITEMAP_SUMMARY_SORT_ORDER',
        'configuration_value' => '100',
        'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
        'configuration_group_id' => '6',
        'sort_order' => '4',
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
    return array('MODULES_SITEMAP_SUMMARY_STATUS',
      'MODULES_SITEMAP_SUMMARY_CONTENT_WIDTH',
      'MODULES_SITEMAP_SUMMARY_SORT_ORDER'
    );
  }
}
