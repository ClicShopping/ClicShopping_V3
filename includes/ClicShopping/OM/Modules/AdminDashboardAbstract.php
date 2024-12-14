<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Modules;

use ClicShopping\OM\Registry;
use ReflectionClass;

/**
 * AdminDashboardAbstract defines the structure and behavior for all Admin Dashboard modules.
 * This class provides a foundation for creating customizable administrative dashboard modules
 * and enforces the implementation of specific methods in derived classes.
 */
abstract class AdminDashboardAbstract implements \ClicShopping\OM\Modules\AdminDashboardInterface
{
  public string $code;
  public $title;
  public $description;
  public int|null $sort_order = null;
  public bool $enabled = false;

  private mixed $db;

  abstract protected function init();

  abstract public function getOutput();

  abstract public function install();

  abstract public function keys();

  final public function __construct()
  {
    $this->code = (new ReflectionClass($this))->getShortName();

    $this->db = Registry::get('Db');

    $this->init();
  }

  public function isEnabled()
  {
    return $this->enabled;
  }

  public function check()
  {
    return isset($this->sort_order);
  }

  public function remove()
  {
    return $this->db->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
  }
}
