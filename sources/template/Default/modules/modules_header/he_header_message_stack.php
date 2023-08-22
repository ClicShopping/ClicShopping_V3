<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  class he_header_message_stack {
    public string $code;
    public string $group;
    public $title;
    public $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;
    public $pages;

    public function __construct()
    {

      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_header_message_stack_title');
      $this->description = CLICSHOPPING::getDef('module_header_message_stack_description');

      if (\defined('MODULE_HEADER_MESSAGE_STACK_STATUS')) {
        $this->sort_order = (int)MODULE_HEADER_MESSAGE_STACK_SORT_ORDER ?? 0;
        $this->enabled = (MODULE_HEADER_MESSAGE_STACK_STATUS == 'True');
      }
    }

    public function execute()
    {

      $CLICSHOPPING_Template = Registry::get('Template');

      if( (isset($_GET['error_message']) && !\is_null($_GET['error_message'])) || (isset($_GET['info_message']) && !\is_null($_GET['info_message']))) {

        $content_width = (int)MODULE_HEADER_MESSAGE_STACK_CONTENT_WIDTH;

        $data ='<!-- Start header Message Stack -->' . "\n";

        if (isset($_GET['error_message'])) {
          $error_message = htmlspecialchars(urldecode(HTML::sanitize($_GET['error_message'])), ENT_QUOTES | ENT_HTML5);
        } else {
          $error_message = '';
        }

        if (isset($_GET['info_message'])) {
          $info_message = htmlspecialchars(urldecode(HTML::sanitize($_GET['info_message'])), ENT_QUOTES | ENT_HTML5);
        } else {
          $info_message = '';
        }
	
        ob_start();

        require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/header_message_stack'));

        $data .= ob_get_clean();

        $data .='<!-- End header Message Stack  -->' . "\n";

        $CLICSHOPPING_Template->addBlock($data, $this->group);
      }

    }

    public function isEnabled()
    {
      return $this->enabled;
    }

    public function check()
    {
      return \defined('MODULE_HEADER_MESSAGE_STACK_STATUS');
    }

    public function install()
    {
      $CLICSHOPPING_Db = Registry::get('Db');


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_HEADER_MESSAGE_STACK_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please, select the width of your module ?',
          'configuration_key' => 'MODULE_HEADER_MESSAGE_STACK_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Indicate a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_HEADER_MESSAGE_STACK_SORT_ORDER',
          'configuration_value' => '10',
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
      return array('MODULE_HEADER_MESSAGE_STACK_STATUS',
                   'MODULE_HEADER_MESSAGE_STACK_CONTENT_WIDTH',
                   'MODULE_HEADER_MESSAGE_STACK_SORT_ORDER',
                  );
    }
  }
