<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM;

use ReflectionClass;

abstract class SitesAbstract implements \ClicShopping\OM\SitesInterface
{
  protected string $code;
  protected string $default_page = 'Home'; // else white page
  protected mixed $page;
  public mixed $app;
  protected mixed $route;
  public int $actions_index = 1;

  abstract protected function init();

  abstract public function setPage();

  final public function __construct()
  {

    $this->code = (new ReflectionClass($this))->getShortName();

    return $this->init();
  }

  /**
   * @return string
   */
  public function getCode(): string
  {
    return $this->code;
  }

  /**
   * @return bool
   */
  public function hasPage(): bool
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
