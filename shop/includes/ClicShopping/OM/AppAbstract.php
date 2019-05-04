<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\OM;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  abstract class AppAbstract {
    public $code;
    public $title;
    public $vendor;
    public $version;
    public $modules = [];

    public $db;
    public $lang;

    abstract protected function init();

    final public function __construct() {
      $this->setInfo();

      $this->db = Registry::get('Db');
      $this->lang = Registry::get('Language');
      $this->init();
    }

    final public function link() {
      $args = func_get_args();

      $parameters = 'A&' . $this->vendor . '\\' . $this->code;

      if (isset($args[0])) {
          $args[0] = $parameters .= '&' . $args[0];
      } else {
          $args[0] = $parameters;
      }

      array_unshift($args, 'index.php');

        return forward_static_call_array([
            'ClicShopping\OM\CLICSHOPPING',
            'link'
        ], $args);
    }

    final public function redirect() {
      $args = func_get_args();

      $parameters = 'A&' . $this->vendor . '\\' . $this->code;

      if (isset($args[0])) {
          $args[0] = $parameters .= '&' . $args[0];
      } else {
          $args[0] = $parameters;
      }

      array_unshift($args, 'index.php');

        return forward_static_call_array([
            'ClicShopping\OM\CLICSHOPPING',
            'redirect'
        ], $args);
    }

    final public function getCode() {
        return $this->code;
    }

    final public function getVendor() {
        return $this->vendor;
    }

    final public function getTitle() {
        return $this->title;
    }

    final public function getVersion() {
        return $this->version;
    }

    final public function getModules() {
        return $this->modules;
    }

    final public function hasModule($module, $type) {
    }

    final private function setInfo() {
      $r = new \ReflectionClass($this);

      $this->code = $r->getShortName();
      $this->vendor = array_slice(explode('\\', $r->getNamespaceName()), -2, 1)[0];

      $metafile = CLICSHOPPING::BASE_DIR . 'Apps/' . $this->vendor . '/' . $this->code . '/clicshopping.json';

      if (!is_file($metafile) || (($json = json_decode(file_get_contents($metafile), true)) === null)) {
          trigger_error('ClicShopping\OM\AppAbstract::setInfo(): ' . $this->vendor . '\\' . $this->code . ' - Could not read App information in ' . $metafile . '.');

          return false;
      }

      $this->title = $json['title'];
      $this->version = $json['version'];

      if (!empty($json['modules'])) {
        $this->modules = $json['modules'];
      }
    }

    final public function getDef() {
      $args = func_get_args();

      if (!isset($args[0])) {
        $args[0] = null;
      }

      if (!isset($args[1])) {
        $args[1] = null;
      }

      if (!isset($args[2])) {
        $args[2] = $this->vendor . '-' . $this->code;
      }

        return call_user_func_array([$this->lang, 'getDef'], $args);
    }

    final public function definitionsExist($group, $language_code = null)
    {
        $language_code = isset($language_code) && $this->lang->exists($language_code) ? $language_code : $this->lang->get('code');

        $pathname = CLICSHOPPING::BASE_DIR . 'Apps/' . $this->vendor . '/' . $this->code . '/languages/' . $this->lang->get('directory', $language_code) . '/' . $group . '.txt';

        if (is_file($pathname)) {
          return true;
        }

      if ($language_code != DEFAULT_LANGUAGE) {
        return call_user_func([$this, __FUNCTION__], $group, DEFAULT_LANGUAGE);
      }

        return false;
    }

    final public function loadDefinitions($group, $language_code = null) {
        $language_code = isset($language_code) && $this->lang->exists($language_code) ? $language_code : $this->lang->get('code');

        if ($language_code != DEFAULT_LANGUAGE) {
          $this->loadDefinitions($group, DEFAULT_LANGUAGE);
        }

        $pathname = CLICSHOPPING::BASE_DIR . 'Apps/' . $this->vendor . '/' . $this->code . '/languages/' . $this->lang->get('directory', $language_code) . '/' . $group . '.txt';

        if (!is_file($pathname)) {
          $language_code = DEFAULT_LANGUAGE;
          $pathname = CLICSHOPPING::BASE_DIR . 'Apps/' . $this->vendor . '/' . $this->code . '/languages/' . $this->lang->get('directory', $language_code) . '/' . $group . '.txt';
        }

        $group = 'Apps/' . $this->vendor . '/' . $this->code . '/' . $group;

        $defs = $this->lang->getDefinitions($group, $language_code, $pathname);

        $this->lang->injectDefinitions($defs, $this->vendor . '-' . $this->code);
    }

    final public function saveCfgParam($key, $value, $title = null, $description = null, $set_func = null) {
        if (is_null($value)) {
            $value = '';
        }

        if (!defined($key)) {
            if (!isset($title)) {
                $title = 'Parameter [' . $this->getTitle() . ']';
            }

            if (!isset($description)) {
                $description = 'Parameter [' . $this->getTitle() . ']';
            }

            $data = [
                'configuration_title' => $title,
                'configuration_key' => $key,
                'configuration_value' => $value,
                'configuration_description' => $description,
                'configuration_group_id' => '6',
                'sort_order' => '0',
                'date_added' => 'now()'
            ];

            if (isset($set_func)) {
                $data['set_function'] = $set_func;
            }

            $this->db->save('configuration', $data);

            define($key, $value);
        } else {
            $this->db->save('configuration', [
                'configuration_value' => $value
            ], [
                'configuration_key' => $key
            ]);
        }
    }

    final public function deleteCfgParam($key)
    {
        $this->db->delete('configuration', [
            'configuration_key' => $key
        ]);
    }
}
