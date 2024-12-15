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

/**
 * Abstract class representing the base structure for modules in the ClicShopping framework.
 *
 * This class defines the foundational methods and properties for modules within the application.
 * It implements basic functionality such as the initialization of the module's code and provides
 * abstract methods that must be implemented by derived classes.
 */
abstract class ModulesAbstract
{
  public string $code;
  protected $interface;
  protected string $ns = 'ClicShopping\Apps\\';

  abstract public function getInfo($app, $key, $data);

  abstract public function getClass($module);

  /**
   * Constructs the object, setting the code property to the short name
   * of the ReflectionClass instance associated with the current class
   * and initializes the object by calling the init method.
   *
   * @return void
   */
  final public function __construct()
  {
    $this->code = (new ReflectionClass($this))->getShortName();

    $this->init();
  }

  /**
   * Initializes the necessary components or sets up the required configurations.
   *
   * @return void
   */
  protected function init()
  {
  }

  /**
   * Filters the provided modules based on the given filter criteria.
   *
   * @param array $modules The array of modules to be filtered.
   * @param mixed $filter The criteria or condition used to filter the modules.
   *
   * @return array The filtered array of modules.
   */
  public function filter($modules, $filter)
  {
    return $modules;
  }
}
