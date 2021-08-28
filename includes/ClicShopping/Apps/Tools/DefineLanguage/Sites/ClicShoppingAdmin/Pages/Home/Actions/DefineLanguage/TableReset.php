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


  namespace ClicShopping\Apps\Tools\DefineLanguage\Sites\ClicShoppingAdmin\Pages\Home\Actions\DefineLanguage;

  use ClicShopping\OM\Cache;
  use ClicShopping\OM\Registry;

  class TableReset extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function __construct()
    {
      $this->app = Registry::get('DefineLanguage');
    }

    public function execute()
    {
// reset all definitions
      $this->app->db->exec('truncate :table_languages_definitions');

// reset cache
      Cache::clear('languages-defs');

      $this->app->redirect('DefineLanguage');
    }
  }