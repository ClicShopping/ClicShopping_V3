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

  abstract class SitesAbstract implements \ClicShopping\OM\SitesInterface
  {
    protected string $code;
    protected string $default_page = 'Home'; // else white page
    protected $page;
    protected $app;
    protected $route;
    public int $actions_index = 1;

    abstract protected function init();

    abstract public function setPage();

    final public function __construct()
    {

      $this->code = (new \ReflectionClass($this))->getShortName();

      return $this->init();
    }

    /**
     * @return string
     */
    public function getCode() :string
    {
      return $this->code;
    }

    /**
     * @return bool
     */
    public function hasPage() :bool
    {
      return isset($this->page);
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
      return $this->page;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
      return $this->route;
    }

    /**
     * @param array $route
     * @param array $routes
     */
    public static function resolveRoute(array $route, array $routes)
    {
    }
  }
