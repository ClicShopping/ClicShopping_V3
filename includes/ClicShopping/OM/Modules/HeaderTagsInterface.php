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

  interface HeaderTagsInterface
  {
    public function getOutput();

    public function install();

    public function keys();

    public function isEnabled();

    public function check();

    public function remove();
  }
