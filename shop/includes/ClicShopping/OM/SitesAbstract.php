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

  abstract class SitesAbstract implements \ClicShopping\OM\SitesInterface {

    protected $code;
    protected $default_page = 'Home'; // else white page
    protected $page;
    protected $app;
    protected $route;
    public $actions_index = 1;

    abstract protected function init();
    abstract public function setPage();

    final public function __construct()  {

      $this->code = (new \ReflectionClass($this))->getShortName();

      return $this->init();
    }

    public function getCode()  {
        return $this->code;
    }

    public function hasPage()  {
      return isset($this->page);
    }

    public function getPage()  {

        return $this->page;
    }

    public function getRoute()  {
        return $this->route;
    }

    public static function resolveRoute(array $route, array $routes) {
    }
  }
