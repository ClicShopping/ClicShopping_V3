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

  class DeleteConfirm implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('SEO')) {
        Registry::set('SEO', new SEOApp());
      }

      $this->app = Registry::get('SEO');
    }

    private function delete($id)
    {
      if (!is_null($id)) {
        $this->app->db->delete('submit_description', ['language_id' => $id]);
      }
    }

    public function execute()
    {
      if (isset($_GET['DeleteConfirm'])) {
        $id = HTML::sanitize($_GET['lID']);
        $this->delete($id);
      }
    }
  }