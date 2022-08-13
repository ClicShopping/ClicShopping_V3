<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTML;

  /**
   * Email password input
   *
   * @param string $password
   * @return string  $password, the password
   *
   * clic_cfg_input_password
   */

  function clic_cfg_set_input_password($password)
  {
    return HTML::passwordField('configuration_value', $password);
  }
