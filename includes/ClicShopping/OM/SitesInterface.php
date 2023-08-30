<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\OM;

interface SitesInterface
{
  public function hasPage();

  public function getPage();

  public function setPage();

  public static function resolveRoute(array $route, array $routes);
}
