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

  namespace ClicShopping\Apps\Marketing\SEO\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\SEO\SEO as SEOApp;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;
    protected $insert_language_id;

    public function __construct()
    {
      if (!Registry::exists('SEO')) {
        Registry::set('SEO', new SEOApp());
      }

      $this->app = Registry::get('SEO');
      $this->insert_language_id = HTML::sanitize($_POST['insert_id']);
      $this->lang = Registry::get('Language');
    }

    private function insert()
    {
      if (isset($this->insert_language_id)) {
        $QsubmitDescription = $this->app->db->get('submit_description', '*', ['language_id' => $this->lang->getId()]);

        while ($QsubmitDescription->fetch()) {
          $cols = $QsubmitDescription->toArray();

          $cols['language_id'] = $this->insert_language_id;

          $this->app->db->save('submit_description', $cols);
        }
      }
    }

    public function execute()
    {
      if (isset($_GET['Insert'])) {
        $this->insert();
      }
    }
  }