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

  namespace ClicShopping\Apps\Tools\ActionsRecorder\Classes\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  class ActionRecorder
  {
    public $_module;
    public $_user_id;
    public $_user_name;

    public function __construct($module, $user_id = null, $user_name = null)
    {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');

      $module = HTML::sanitize(str_replace(' ', '', $module));

      if (\defined('MODULE_ACTION_RECORDER_INSTALLED') && !\is_null(MODULE_ACTION_RECORDER_INSTALLED)) {
        if (!\is_null($module) && in_array($module . '.' . substr(CLICSHOPPING::getIndex(), (strrpos(CLICSHOPPING::getIndex(), '.') + 1)), explode(';', MODULE_ACTION_RECORDER_INSTALLED))) {
          if (!class_exists($module)) {
            if (is_file($CLICSHOPPING_Template->getModuleDirectory() . '/action_recorder/' . $module . '.' . substr(CLICSHOPPING::getIndex(), (strrpos(CLICSHOPPING::getIndex(), '.') + 1)))) {
              $CLICSHOPPING_Language->loadDefinitions('modules/action_recorder/' . $module);
              include($CLICSHOPPING_Template->getModuleDirectory() . '/action_recorder/' . $module . '.' . substr(CLICSHOPPING::getIndex(), (strrpos(CLICSHOPPING::getIndex(), '.') + 1)));
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

      $this->_module = $module;

      if (!empty($user_id) && is_numeric($user_id)) {
        $this->_user_id = $user_id;
      }

      if (!empty($user_name)) {
        $this->_user_name = $user_name;
      }

      $GLOBALS[$this->_module] = new $module();
      $GLOBALS[$this->_module]->setIdentifier();
    }

    /**
     * @param $module
     * @return mixed
     */
    public function getModule()
    {
      return $GLOBALS[$this->_module];
    }

    /**
     * @return false
     */
    public function canPerform()
    {
      if (!\is_null($this->_module)) {
        return $this->getModule()->canPerform($this->_user_id, $this->_user_name);
      }

      return false;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
      if (!\is_null($this->_module)) {
        return $this->getModule()->title;
      }
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
      if (!\is_null($this->_module)) {
        return $this->getModule()->identifier;
      }
    }

    /**
     * @param bool $success
     */
    public function record(bool $success = true)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if (!\is_null($this->_module)) {
        if ($success === true) {
          $success = 1;
        } else {
          $success = 0;
        }

        $sql_array = [
          'module' => $this->_module,
          'user_id' => (int)$this->_user_id,
          'user_name' => $this->_user_name,
          'identifier' => $this->getIdentifier(),
          'success' => $success,
          'date_added' => 'now()'
        ];

        $CLICSHOPPING_Db->save('action_recorder', $sql_array);
      }
    }

    /**
     * @return mixed
     */
    public function expireEntries()
    {
      if (!\is_null($this->_module)) {
        return $this->getModule()->expireEntries();
      }
    }
  }
