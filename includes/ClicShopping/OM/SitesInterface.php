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

/**
 * Interface SitesInterface
 *
 * Defines the structure for handling pages and resolving routes
 * in the context of the application.
 */
interface SitesInterface
{
  public function hasPage();

  public function getPage();

  public function setPage();

  public static function resolveRoute(array $route, array $routes);
}
