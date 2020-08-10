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

  namespace ClicShopping\OM;

  abstract class ModulesAbstract
  {
    public string $code;
    protected $interface;
    protected string $ns = 'ClicShopping\Apps\\';

    abstract public function getInfo($app, $key, $data);

    abstract public function getClass($module);

    final public function __construct()
    {
      $this->code = (new \ReflectionClass($this))->getShortName();

      $this->init();
    }

    protected function init()
    {
    }

    /**
     * @param $modules
     * @param $filter
     * @return mixed
     */
    public function filter($modules, $filter)
    {
      return $modules;
    }
  }
