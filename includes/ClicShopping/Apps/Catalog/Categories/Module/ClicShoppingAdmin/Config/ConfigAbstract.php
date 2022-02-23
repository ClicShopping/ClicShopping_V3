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

  namespace ClicShopping\Apps\Catalog\Categories\Module\ClicShoppingAdmin\Config;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  abstract class ConfigAbstract
  {
    protected mixed $app;

    public string $code;
    public $title;
    public string $short_title;
    public string $introduction;
    public array $req_notes = [];
    public bool $is_installed = false;
    public bool $is_uninstallable = false;
    public bool $is_migratable = false;
    public ?int $sort_order = 0;

    abstract protected function init();

    final public function __construct()
    {
      $this->app = Registry::get('Categories');

      $this->code = (new \ReflectionClass($this))->getShortName();

      $this->app->loadDefinitions('module/' . $this->code . '/' . $this->code);

      $this->init();
    }

    public function install()
    {
      $cut_length = \strlen('CLICSHOPPING_APP_CATEGORIES_' . $this->code . '_');

      foreach ($this->getParameters() as $key) {
        $p = strtolower(substr($key, $cut_length));

        $class = 'ClicShopping\Apps\Catalog\Categories\Module\ClicShoppingAdmin\Config\\' . $this->code . '\Params\\' . $p;

        $cfg = new $class($this->code);

        $this->app->saveCfgParam($key, $cfg->default, $cfg->title ?? null, $cfg->description ?? null, $cfg->set_func ?? null);
      }
    }

    public function uninstall()
    {
      $Qdelete = $this->app->db->prepare('delete from :table_configuration
                                          where configuration_key
                                          like :configuration_key
                                          ');
      $Qdelete->bindValue(':configuration_key', 'CLICSHOPPING_APP_CATEGORIES_' . $this->code . '_%');
      $Qdelete->execute();

      return $Qdelete->rowCount();
    }

    public function getParameters()
    {
      $result = [];

      $directory = CLICSHOPPING::BASE_DIR . 'Apps/Catalog/Categories/Module/ClicShoppingAdmin/Config/' . $this->code . '/Params';

      if ($dir = new \DirectoryIterator($directory)) {
        foreach ($dir as $file) {
          if (!$file->isDot() && !$file->isDir() && ($file->getExtension() == 'php')) {
            $class = 'ClicShopping\Apps\Catalog\Categories\Module\ClicShoppingAdmin\Config\\' . $this->code . '\\Params\\' . $file->getBasename('.php');

            if (is_subclass_of($class, 'ClicShopping\Apps\Catalog\Categories\Module\ClicShoppingAdmin\Config\ConfigParamAbstract')) {
              $result[] = 'CLICSHOPPING_APP_CATEGORIES_' . $this->code . '_' . strtoupper($file->getBasename('.php'));
            } else {
              trigger_error('ClicShopping\Apps\Catalog\Categories\Module\ClicShoppingAdmin\Config\\ConfigAbstract::getParameters(): ClicShopping\Apps\Catalog\Categories\Module\ClicShoppingAdmin\Config\\' . $this->code . '\\Params\\' . $file->getBasename('.php') . ' is not a subclass of ClicShopping\Apps\Catalog\Categories\Module\ClicShoppingAdmin\Config\ConfigParamAbstract and cannot be loaded.');
            }
          }
        }
      }

      return $result;
    }

    public function getInputParameters()
    {
      $result = [];

      $cut = 'CLICSHOPPING_APP_CATEGORIES_' . $this->code . '_';

      $cut_length = \strlen($cut);

      foreach ($this->getParameters() as $key) {
        $p = strtolower(substr($key, $cut_length));

        $class = 'ClicShopping\Apps\Catalog\Categories\Module\ClicShoppingAdmin\Config\\' . $this->code . '\Params\\' . $p;

        $cfg = new $class($this->code);


        if (!\defined($key)) {
          $this->app->saveCfgParam($key, $cfg->default, $cfg->title ?? null, $cfg->description ?? null, $cfg->set_func ?? null);
        }

        if ($cfg->app_configured !== false) {
          if (is_numeric($cfg->sort_order)) {
            $counter = (int)$cfg->sort_order;
          } else {
            $counter = \count($result);
          }

          while (true) {
            if (isset($result[$counter])) {
              $counter++;

              continue;
            }

            $set_field = $cfg->getSetField();

            if (!empty($set_field)) {
              $result[$counter] = $set_field;
            }

            break;
          }
        }
      }

      ksort($result, SORT_NUMERIC);

      return $result;
    }
  }
