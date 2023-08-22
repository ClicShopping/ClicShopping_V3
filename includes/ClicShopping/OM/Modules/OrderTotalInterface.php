<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM\Modules;

  interface OrderTotalInterface
  {
    public function process();

    public function check()
    {;

    public function install();

    public function remove();

    public function keys();
  }
