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

  namespace ClicShoppng\OM;

  abstract class RegistryAbstract
  {
    protected $alias;
    protected $value;

    public function getValue()
    {
      return $this->value;
    }

    public function hasAlias(): bool
    {
      return isset($this->alias);
    }

    public function getAlias(): string
    {
      return $this->alias;
    }
  }