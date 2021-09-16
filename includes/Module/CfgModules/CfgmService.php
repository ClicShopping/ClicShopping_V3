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

  use ClicShopping\OM\CLICSHOPPING;

  class CfgmService
  {
    public string $code = 'service';
    public string $directory;
    public string $site = 'Shop';
    public string $key = 'MODULE_SERVICES_INSTALLED';
    public string $title;
    public $language_directory;
    public bool $template_integration = false;

    public function __construct()
    {

      $this->directory = CLICSHOPPING::BASE_DIR . 'Service/Shop/';
    }
  }