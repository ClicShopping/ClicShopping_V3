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

  namespace ClicShopping\Sites\ClicShoppingAdmin;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;


  class ActionRecorderAdmin extends \ClicShopping\Apps\Tools\ActionsRecorder\Classes\Shop\ActionRecorder
  {
    protected $lang;

    public function __construct(string $module, string $user_id = null, string $user_name = null)
    {
      $this->lang = Registry::get('Language');

      $module = HTML::sanitize(str_replace(' ', '', $module));

      $this->_module = $module;

      $this->isInstalled();

      if (!empty($user_id) && is_numeric($user_id)) {
        $this->_user_id = $user_id;
      }

      if (!empty($user_name)) {
        $this->_user_name = $user_name;
      }

      $GLOBALS[$this->_module] = new $module();
      $GLOBALS[$this->_module]->setIdentifier();
    }

    public function isInstalled()
    {
      $module = HTML::sanitize(str_replace(' ', '', $this->_module ));

      if (defined('MODULE_ACTION_RECORDER_INSTALLED') && !is_null(MODULE_ACTION_RECORDER_INSTALLED)) {
        if (!is_null($module) && in_array($module . '.' . substr(CLICSHOPPING::getIndex(), (strrpos(CLICSHOPPING::getIndex(), '.') + 1)), explode(';', MODULE_ACTION_RECORDER_INSTALLED))) {

          if (!class_exists($module)) {
            if (is_file(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/modules/action_recorder/' . $module . '.' . substr(CLICSHOPPING::getIndex(), (strrpos(CLICSHOPPING::getIndex(), '.') + 1)))) {

              $this->lang->loadDefinitions('Shop/Module/ActionRecorder/' . $module);

              include_once(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/modules/action_recorder/' . $module . '.' . substr(CLICSHOPPING::getIndex(), (strrpos(CLICSHOPPING::getIndex(), '.') + 1)));
            } else {
              return false;
            }
          }
        } else {
          return false;
        }
      } else {
        return false;
      }
    }
  }
