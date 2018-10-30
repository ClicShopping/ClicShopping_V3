<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */


  namespace ClicShopping\Apps\Configuration\Langues\Sites\ClicShoppingAdmin\Pages\Home\Actions\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  use ClicShopping\Apps\Configuration\Langues\Classes\ClicShoppingAdmin\Status;


  class SetFlag extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('Langues');
    }

    public function execute() {

      Status::getLanguageStatus($_GET['lid'], $_GET['flag']);

// Verifie si les status ne sont pas tous en off
      $QcountLanguages = $this->app->db->prepare('select count(status) as status
                                                  from :table_languages
                                                  where status = 1
                                                ');
      $QcountLanguages->execute();

      if ($QcountLanguages->value('status') == 0)  {
        $Qupdate = $this->app->db->prepare('update :table_languages
                                            set status = 1
                                            where languages_id = :languages_id
                                          ');
        $Qupdate->bindInt(':languages_id', (int)$_GET['lid']);
        $Qupdate->execute();
      }

      Cache::clear('languages-system-shop');
      Cache::clear('languages-system-admin');

      $this->app->redirect('Langues&'. (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : ''). 'lID=' . $_GET['lID']);
    }
  }