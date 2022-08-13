<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\SEO\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\SEO\SEO as SEOApp;
  use ClicShopping\Apps\Configuration\Langues\Classes\ClicShoppingAdmin\LanguageAdmin;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;
    protected mixed $lang;

    public function __construct()
    {
      if (!Registry::exists('SEO')) {
        Registry::set('SEO', new SEOApp());
      }

      $this->app = Registry::get('SEO');
      $this->lang = Registry::get('Language');
    }

    private function insert()
    {
      $insert_language_id = LanguageAdmin::getLatestLanguageID();
      $QsubmitDescription = $this->app->db->get('submit_description', '*', ['language_id' => $this->lang->getId()]);

      while ($QsubmitDescription->fetch()) {
        $cols = $QsubmitDescription->toArray();

        $cols['language_id'] = (int)$insert_language_id;

        $this->app->db->save('submit_description', $cols);
      }
    }

    public function execute()
    {
     if (!\defined('CLICSHOPPING_APP_SEO_SE_STATUS') || CLICSHOPPING_APP_SEO_SE_STATUS == 'False') {
       return false;
     }

      if (isset($_GET['Langues'], $_GET['Insert'])) {
        $this->insert();
      }
    }
  }