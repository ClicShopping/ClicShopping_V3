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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class CfgModulesAdmin
  {

    public $_modules = [];
    protected $lang;

    public function __construct()
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      $file_extension = substr(CLICSHOPPING::getIndex(), strrpos(CLICSHOPPING::getIndex(), '.'));
      $directory = $CLICSHOPPING_Template->getModulesDirectory() . '/Module/CfgModules/';

      if ($dir = @dir($directory)) {
        while ($file = $dir->read()) {
          if (!is_dir($directory . $file)) {
            if (substr($file, strrpos($file, '.')) == $file_extension) {
              $class = substr($file, 0, strrpos($file, '.'));

              include($CLICSHOPPING_Template->getModulesDirectory() . '/Module/CfgModules/' . $class . '.php');

              $m = new $class();

              if (is_object($m)) {
                $this->_modules[] = ['code' => $m->code,
                  'directory' => $m->directory,
                  'language_directory' => $m->language_directory,
                  'key' => $m->key,
                  'title' => $m->title,
                  'template_integration' => $m->template_integration,
                  'site' => $m->site
                ];
              }
            }
          }
        }
      }
    }

    public function getAll()
    {
      return $this->_modules;
    }

    public function get($code, $key)
    {
      if (is_array($this->_modules)) {
        foreach ($this->_modules as $m) {
          if ($m['code'] == $code) {
            return $m[$key];
          }
        }
      }
    }

    public function exists($code)
    {
      if (is_array($this->_modules)) {
        foreach ($this->_modules as $m) {
          if ($m['code'] == $code) {
            return true;
          }
        }
      }

      return false;
    }


    public function countModules($modules = '')
    {
      $count = 0;

      if (empty($modules)) return $count;

      $modules_array = explode(';', $modules);

      for ($i = 0, $n = count($modules_array); $i < $n; $i++) {
        $class = substr($modules_array[$i], 0, strrpos($modules_array[$i], '.'));

        if (isset($GLOBALS[$class]) && is_object($GLOBALS[$class])) {
          if ($GLOBALS[$class]->enabled) {
            $count++;
          }
        }
      }

      return $count;
    }
  }