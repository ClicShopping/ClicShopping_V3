<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;

class CfgmService
{
  public string $code = 'service';
  public string $directory;
  public string $site = 'Shop';
  public string $key = 'MODULE_SERVICES_INSTALLED';
  public $title;
  public $language_directory;
  public bool $template_integration = false;

  public function __construct()
  {

    $this->directory = CLICSHOPPING::BASE_DIR . 'Service/Shop/';
  }
}